<?php

namespace App\Controller;

use App\Entity\Spider;
use App\Service\SpiderStatusCalculator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SpiderController extends AbstractController
{
    #[Route('/spinne/{name}', name: 'app_spider_show')]
    public function show(#[MapEntity(mapping: ['name' => 'name'])] Spider $spider, SpiderStatusCalculator $statusCalculator): Response
    {
        $lifeStage = $statusCalculator->currentLifeStage($spider);
        $lastFeedingDate = $statusCalculator->lastFeedingDate($spider);
        $status = $statusCalculator->feedingStatus($spider, $lastFeedingDate, $lifeStage);
        $daysUntilDue = $statusCalculator->daysUntilDue($spider, $lastFeedingDate, $lifeStage);
        $daysSinceReference = $statusCalculator->daysSinceReference($spider, $lastFeedingDate);

        $latestMolt = null;
        foreach ($spider->getMolts() as $molt) {
            if ($latestMolt === null || $molt->getDate() > $latestMolt->getDate()) {
                $latestMolt = $molt;
            }
        }

        $today = new \DateTimeImmutable();
        $events = [];

        foreach ($spider->getFeedings() as $feeding) {
            $events[] = [
                'type' => 'feeding',
                'date' => $feeding->getDate(),
                'daysAgo' => $today->diff($feeding->getDate())->days,
                'food' => $feeding->getFood(),
                'quantity' => $feeding->getQuantity(),
            ];
        }

        foreach ($spider->getMolts() as $molt) {
            $events[] = [
                'type' => 'molt',
                'date' => $molt->getDate(),
                'daysAgo' => $today->diff($molt->getDate())->days,
                'lifeStage' => $molt->getLifeStage(),
                'legSpanCm' => $molt->getLegSpanCm(),
                'notes' => $molt->getNotes(),
            ];
        }

        usort($events, fn(array $a, array $b) => $b['date'] <=> $a['date']);

        return $this->render('spider/show.html.twig', [
            'spider' => $spider,
            'lifeStage' => $lifeStage,
            'legSpanCm' => $latestMolt?->getLegSpanCm(),
            'status' => $status,
            'daysUntilDue' => $daysUntilDue,
            'daysSinceReference' => $daysSinceReference,
            'events' => $events,
        ]);
    }
}
