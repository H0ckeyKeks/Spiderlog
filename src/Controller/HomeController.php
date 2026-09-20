<?php

namespace App\Controller;

use App\Repository\SpiderRepository;
use App\Service\SpiderStatusCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SpiderRepository $spiderRepository, SpiderStatusCalculator $statusCalculator): Response
    {
        $spiders = $spiderRepository->findBy(['archivedAt' => null], ['name' => 'ASC']);

        $entries = [];
        foreach ($spiders as $spider) {
            $lifeStage = $statusCalculator->currentLifeStage($spider);
            $lastFeedingDate = $statusCalculator->lastFeedingDate($spider);

            $entries[] = [
                'spider' => $spider,
                'lifeStage' => $lifeStage,
                'lastFeedingDate' => $lastFeedingDate,
                'lastFeedingDays' => $lastFeedingDate ? (new \DateTimeImmutable())->diff($lastFeedingDate)->days : null,
                'daysSinceReference' => $statusCalculator->daysSinceReference($spider, $lastFeedingDate),
                'status' => $statusCalculator->feedingStatus($spider, $lastFeedingDate, $lifeStage),
                'daysUntilDue' => $statusCalculator->daysUntilDue($spider, $lastFeedingDate, $lifeStage),
            ];
        }

        return $this->render('home/index.html.twig', [
            'entries' => $entries,
            'greeting' => $this->greeting(),
        ]);
    }

    private function greeting(): string
    {
        $hour = (int) (new \DateTimeImmutable())->format('G');

        return match(true) {
            $hour < 12 => 'Guten Morgen',
            $hour < 18 => 'Guten Tag',
            default => 'Guten Abend',
        };
    }
}
