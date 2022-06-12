<?php


namespace App\Services\v1;


use App\Models\Calendar;
use DateInterval;
use DateTime;

class CalendarService
{

    protected $calendarModel;

    public function __construct(Calendar $calendar)
    {
        $this->calendarModel = $calendar;
    }

    public function handleShow(array $request)
    {
        $calendarEvents = $this->calendarModel
            ->where('date_start', '>', $request['date_start'])
            ->where('date_end', '<', $request['date_end'])
            ->get();

        return $calendarEvents;
    }


    public function countMinutesBetweenDates($to, $from)
    {
        $to = strtotime($to);
        $from = strtotime($from);

        $seconds = $to - $from;

        return floor($seconds / 60);
    }

    public function uncheckUserDate($dateStart,$dateEnd){
        $dateEnd = explode(' ',$dateEnd);
        $dateStart = explode(' ',$dateStart);
        $newDate['date_end'] = $dateEnd[0] . ' 00:00';
        $newDate['date_start'] = $dateStart[0] . ' 00:00';

        if($newDate['date_start'] == $newDate['date_end']){
            $date = new DateTime($newDate['date_start']);
            $date->add(new DateInterval('PT' . 1440 . 'M'));
            $newDate['date_end'] = $date->format('Y-m-d H:i');
        }

        $resultDates = [
            'date_end'=>$newDate['date_end'],
            'date_start'=>$newDate['date_start']
        ];
        return $resultDates;
    }

    public function setDate($updatedField, $calendarEvent)
    {
        if (!array_key_exists('duration', $updatedField)) {

            if (array_key_exists('date_start', $updatedField) && array_key_exists('date_end', $updatedField)) {
                $updatedField['duration'] = $this->countMinutesBetweenDates($updatedField['date_end'], $updatedField['date_start']);
            } elseif (array_key_exists('date_start', $updatedField) && !array_key_exists('date_end', $updatedField)) {
                $updatedField['duration'] = $this->countMinutesBetweenDates($calendarEvent['date_end'], $updatedField['date_start']);
            } elseif (!array_key_exists('date_start', $updatedField) && array_key_exists('date_end', $updatedField)) {
                $updatedField['duration'] = $this->countMinutesBetweenDates($updatedField['date_end'], $calendarEvent['date_start']);
            }
        } else {
            $dateStart = (array_key_exists('date_start', $updatedField)) ? $updatedField['date_start'] : $calendarEvent['date_start'];
            $newDate = new DateTime($dateStart);
            $newDate->add(new DateInterval('PT' . $updatedField['duration'] . 'M'));
            $updatedField['date_end'] = $newDate->format('Y-m-d H:i');

        }
        return $updatedField;
    }

    public function getError($id)
    {
        if ($id == 1) {
            $error = [
                'message' => 'You can\'t update events that are less than 3 hours away'
            ];
        } elseif ($id == 2) {
            $error = [
                'message' => 'Dates entered incorrectly'
            ];
        } elseif ($id == 3){
            $error = [
                'message' => 'You can\'t delete events that are less than 3 hours away'
            ];
        }

        return json_encode($error);
    }

}
