<?php

namespace UserBundle\Twig;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class DateFormatExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [];
    }

    public function getFunctions()
    {
        return [ // Twig_SimpleFunction
            'formatDateRange' => new \Twig_SimpleFunction('formatDateRange', array($this, 'formatDateRange')),
            'formatTimeRange' => new \Twig_SimpleFunction('formatTimeRange', array($this, 'formatTimeRange')),
            'diffDateRange' => new \Twig_SimpleFunction('diffDateRange', array($this, 'diffDateRange')),
        ];
    }

    public function diffDateRange($startDate, $endDate)
    {
        // $interval = new \DateInterval('PT2H');
        //create periods every hour between the two dates
        // $periods = new \DatePeriod($startDate, $interval, $endDate);
        //count the number of objects within the periods
        // $hours = iterator_count($periods);

        $diff = $endDate->diff($startDate);
        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24) + ($diff->i / 60);

        // return $diff->format('%a days, %h hours, %i minutes');
        return $hours.'h';
    }

    public function formatTimeRange($startDate, $endDate)
    {
        return $startDate->format('H:i').' - '.$endDate->format('H:i');
    }

    public function formatDateRange($startDate, $endDate)
    {
        $formatted_date = null;

        // same year
        if ($startDate->format('Y') === $endDate->format('Y')) {

            // same month
            if ($startDate->format('F') === $endDate->format('F')) {

                // same day
                if ($startDate->format('d') === $endDate->format('d')) {
                    $formatted_date = $startDate->format('j/m/Y');
                } else {
                    $formatted_date = $startDate->format('j').'-'.$endDate->format('j').', '.$startDate->format('m/Y');
                }
            } else {
                $formatted_date = $startDate->format('j/m').' - '.$endDate->format('j/m').', '.$startDate->format('Y');
            }
        } else {
            $formatted_date = $startDate->format('j/m/Y').' - '.$endDate->format('j/m/Y');
        }

        return $formatted_date;
    }

    public function getName()
    {
        return 'date_format_extension';
    }
}
