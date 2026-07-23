<?php

namespace App\Controller;

use App\Entity\LifeStage;
use App\Entity\Spider;
use App\Repository\SpiderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    private const ACCLIMATION_DAYS = 7;


    #[Route('/', name: 'app_home')]
    public function index(Request $request, SpiderRepository $spiderRepository): Response
    {
        // Collection of all spiders that are not archived
        $spiders = $spiderRepository->findBy(['archivedAt' => null], ['name' => 'ASC']);

        // Get spiderId from query parameter, if present
        $selectedId = $request->query->get('spider');
        // selected = spider entity
        $selected = $selectedId ? $spiderRepository->find($selectedId) : null;

        if (!$selected && count($spiders) > 0) {
            $selected = $spiders[0];
        }

        // Add help methods
        if($selected !== null)
        {
            $currentLifeStage = $this->determineCurrentLifeStage($selected);
            $lastFeedingDate = $this->findLastFeedingDate($selected);
            $needsFeeding = $this->needsFeeding($selected, $lastFeedingDate, $currentLifeStage);
        }
        else
        {
            $currentLifeStage = null;
            $lastFeedingDate = null;
            $needsFeeding = null;
        }

        return $this->render('home/index.html.twig', [
            'spiders' => $spiders,
            'selected' => $selected,
            'currentLifeStage' => $currentLifeStage,
            'lastFeedingDate' => $lastFeedingDate,
            'needsFeeding' => $needsFeeding,
        ]);
    }


    private function determineCurrentLifeStage(Spider $spider): LifeStage
    {
        // Check if there are Molts
        if (!$spider->getMolts()->isEmpty())
        {
            // Get current entry
            $latestMolt = $spider->getMolts()->first();

            foreach ($spider->getMolts() as $molt) {
                if ($molt->getDate() > $latestMolt->getDate())
                {
                    $latestMolt = $molt;
                }
            }
            $currentLifeStage = $latestMolt->getLifeStage();
        }
        else
        {
            $currentLifeStage = $spider->getInitialLifeStage();
        }

        return $currentLifeStage;
    }


    private function findLastFeedingDate(Spider $spider): ?\DateTimeImmutable
    {
        if (!$spider->getFeedings()->isEmpty()) {
            // Get current entry
            $latestFeeding = $spider->getFeedings()->first();

            foreach ($spider->getFeedings() as $feeding) {
                if ($feeding->getDate() > $latestFeeding->getDate()) {
                    $latestFeeding = $feeding;
                }
            }
            $latestFeedingDate = $latestFeeding->getDate();
        }
        else
        {
            $latestFeedingDate = null;
        }

        return $latestFeedingDate;
    }


    private function needsFeeding(Spider $spider, ?\DateTimeImmutable $lastFeedingDate, LifeStage $currentLifeStage): bool
    {
        if ($lastFeedingDate === null)
        {
            $referenceDate = $spider->getDateAquired();
            // How many days until signal will be red
            $threshold = self::ACCLIMATION_DAYS;
        }
        else
        {
            $referenceDate = $lastFeedingDate;
            // Placeholder for threshold -> null
            $threshold = null;

            $feedingSchedules = $spider->getSpecies()->getFeedingSchedules();
            foreach ($feedingSchedules as $feedingSchedule)
            {
                if($feedingSchedule->getLifeStage() === $currentLifeStage)
                {
                    $threshold = $feedingSchedule->getIntervalDays();
                    break;
                }
            }

            if ($threshold === null)
            {
                // No FeedingSchedule for this Species / LifeStage set
                return true;
            }
        }

        $today = new \DateTimeImmutable();
        $daysSinceReference = $today->diff($referenceDate)->days;

        return $daysSinceReference > $threshold;
    }
}
