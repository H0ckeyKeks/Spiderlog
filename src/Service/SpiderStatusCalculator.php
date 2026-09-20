<?php

namespace App\Service;

use App\Entity\LifeStage;
use App\Entity\Spider;
use App\Enum\FeedingStatus;

final class SpiderStatusCalculator
{
    /*
     * DUE_SOON_FRACTION = Share of the species feeding interval used as lead time for the due soon (yellow) state,
     * in this case 0.2 = 20 % of the interval
     *
     * Min_Days makes sure slings with short intervals still get some warning
     *
     * Max_Days makes sure that adults with long intervals don't turn yellow way too early
     */
    private const DUE_SOON_FRACTION = 0.2;
    private const DUE_SOON_MIN_DAYS = 1;
    private const DUE_SOON_MAX_DAYS = 5;

    /*
     * Determines the spiders current life stage by looking at its molts and taking the stage
     * of the most recent ones. If there are no molts, it falls back to the initial life stage
     * where the spider was created in the app.
     */
    public function currentLifeStage(Spider $spider): LifeStage
    {
        if (!$spider->getMolts()->isEmpty())
        {
            $latestMolt = $spider->getMolts()->first();
            foreach ($spider->getMolts() as $molt)
            {
                if ($molt->getDate() > $latestMolt->getDate())
                {
                    $latestMolt = $molt;
                }
            }

            return $latestMolt->getLifeStage();
        }

        return $spider->getInitialLifeStage();
    }

    /*
     * Finds the most recent feeding date and returns null if the spider has never been fed
     */
    public function lastFeedingDate(Spider $spider): ?\DateTimeImmutable
    {
        if ($spider->getFeedings()->isEmpty())
        {
            return null;
        }

        $latestFeeding = $spider->getFeedings()->first();
        foreach ($spider->getFeedings() as $feeding)
        {
            if ($feeding->getDate() > $latestFeeding->getDate())
            {
                $latestFeeding = $feeding;
            }
        }

        return $latestFeeding->getDate();
    }

    /*
     * Looks through the species FeedingSchedule entries for one matching the current life stage and
     * returns its interval in days. Returns null if nothing is configured for that species and stage
     * combination.
     */
    private function resolveThreshold(Spider $spider, LifeStage $currentLifeStage): ?int
    {
        foreach ($spider->getSpecies()->getFeedingSchedules() as $feedingSchedule)
        {
            if ($feedingSchedule->getLifeStage() === $currentLifeStage)
            {
                return $feedingSchedule->getIntervalDays();
            }
        }

        return null;
    }

    /*
     * Turns the interval into the actual due soon lead time days
     */
    private function dueSoonDays(int $threshold): int
    {
        $days = (int) round($threshold * self::DUE_SOON_FRACTION);

        return max(self::DUE_SOON_MIN_DAYS, min(self::DUE_SOON_MAX_DAYS, $days));
    }

    /*
     * Helper function that does the acutal math:
     * It substracts the reference date (last feeding or aquisition date if the spider has never been fed) from today and then
     * substracts it from threshold. Negative result means overdue. Returns null if there is no threshold.
     */
    private function computeDaysRemaining(Spider $spider, ?\DateTimeImmutable $lastFeedingDate, LifeStage $currentLifeStage): ?int
    {
        $threshold = $this->resolveThreshold($spider, $currentLifeStage);
        if ($threshold === null)
        {
            return null; // kein FeedingSchedule für Art/Lebensstadium hinterlegt
        }

        $referenceDate = $lastFeedingDate ?? $spider->getDateAquired();
        $today = new \DateTimeImmutable();
        $daysSinceReference = $today->diff($referenceDate)->days;

        return $threshold - $daysSinceReference;
    }

    /*
     * Returns the traffic light status for the template. Uses computeDaysRemaining() and compares it against dueSoonDays().
     */
    public function feedingStatus(Spider $spider, ?\DateTimeImmutable $lastFeedingDate, LifeStage $currentLifeStage): FeedingStatus
    {
        $threshold = $this->resolveThreshold($spider, $currentLifeStage);
        if ($threshold === null)
        {
            return FeedingStatus::Overdue;
        }

        $daysRemaining = $this->computeDaysRemaining($spider, $lastFeedingDate, $currentLifeStage);

        return match(true)
        {
            $daysRemaining < 0 => FeedingStatus::Overdue,
            $daysRemaining <= $this->dueSoonDays($threshold) => FeedingStatus::DueSoon,
            default => FeedingStatus::Ok,
        };
    }

    /*
     * Used for the "due in X days" display in the template
     */
    public function daysUntilDue(Spider $spider, ?\DateTimeImmutable $lastFeedingDate, LifeStage $currentLifeStage): ?int
    {
        return $this->computeDaysRemaining($spider, $lastFeedingDate, $currentLifeStage);
    }

    /*
     * Calculates number of days since last feeding or aquisition. Used for the "overdue by X days" display in the
     * template.
     */
    public function daysSinceReference(Spider $spider, ?\DateTimeImmutable $lastFeedingDate): int
    {
        $referenceDate = $lastFeedingDate ?? $spider->getDateAquired();
        $today = new \DateTimeImmutable();

        return $today->diff($referenceDate)->days;
    }
}
