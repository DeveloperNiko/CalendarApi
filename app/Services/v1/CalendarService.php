<?php


namespace App\Services\v1;


use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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


    public function countDateEnd($dateStart, $duration)
    {
        $date = new Carbon($dateStart);
        $dateStart = $date->parse($dateStart)->format('Y-m-d H:i');
        $dateEnd = $date->createFromFormat('Y-m-d H:i',$dateStart)->addMinutes($duration);

        if(Auth::check()){
        return $dateEnd->toDateTimeString();
        }
        return $dateEnd->toDateString();
    }

    public function uncheckUserDate($newTask){
        $dateEnd = explode(' ',$newTask['date_end']);
        $dateStart = explode(' ',$newTask['date_start']);
        $newTask['date_end'] = $dateEnd[0];
        $newTask['date_start'] = $dateStart[0];

        if($newTask['date_start'] == $newTask['date_end']) {
            $newTask['date_end'] = $this->countDateEnd($newTask['date_end'],1440);
        }

        return $newTask;
    }

    public function setDate($updatedField, $calendarEvent)
    {
        $dateStart = $calendarEvent['date_start'];
        $dateEnd = $calendarEvent['date_end'];

        if (array_key_exists('date_start', $updatedField)) {
            $dateStart = $updatedField['date_start'];
        }
        if (array_key_exists('date_end', $updatedField)) {
            $dateEnd = $updatedField['date_end'];
        }

        if (array_key_exists('duration', $updatedField)) {
            $updatedField['date_end'] = $this->countDateEnd($dateStart,$updatedField['duration']);
            return $updatedField;
        }

        $current = Carbon::now();
        $date = Carbon::parse($dateEnd);
        $updatedField['duration'] = $date->diffInMinutes($current);
        $updatedField['date_end'] = $this->countDateEnd($dateStart,$calendarEvent['duration']);

        return $updatedField;
    }

    public function getError($id)
    {
        switch ($id){
            case '1' :
                $error = [
                    'message' => 'You can\'t update events that are less than 3 hours away'
                ];
                break;
            case '2' :
                $error = [
                    'message' => 'Dates entered incorrectly'
                ];
                break;
            case '3' :
                $error = [
                    'message' => 'You can\'t delete events that are less than 3 hours away'
                ];
        }

        return json_encode($error);
    }

    public function getDifferenceTime($time)
    {
       $diffTime = Carbon::parse($time)->diffInHours(Carbon::now());

        return $diffTime;
    }

}
