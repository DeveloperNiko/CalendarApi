<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\requests\v1\Calendar\CreateRequest;
use App\Http\requests\v1\Calendar\DestroyRequest;
use App\Http\requests\v1\Calendar\ShowRequest;
use App\Http\requests\v1\Calendar\UpdateRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use App\Services\v1\CalendarService;

class CalendarController extends Controller
{

    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Display the specified resource.
     *
     * @param ShowRequest $showRequest
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $showRequest)
    {
        return $this->calendarService->handleShow($showRequest->all());
    }

    /**
     * Show the form for creating a new resource.
     * @param CreateRequest $createRequest
     * @return CalendarResource
     */
    public function create(CreateRequest $createRequest)
    {
        $newTask = $createRequest->all();
        if(!$createRequest->check()){
            $newDates = $this->calendarService->uncheckUserDate($newTask['date_start'],$newTask['date_end']);
            $newTask['date_start'] = $newDates['date_start'];
            $newTask['date_end'] = $newDates['date_end'];
        }
        $newTask['duration'] = $this->calendarService->countMinutesBetweenDates($newTask['date_end'], $newTask['date_start']);
        $newTask = Calendar::create($newTask);

        return new CalendarResource($newTask);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $updateRequest
     * @param int $id
     * @return CalendarResource|String
     */
    public function update(UpdateRequest $updateRequest, $id)
    {
        $calendarEvent = Calendar::find($id);
        $diffTime = $this->calendarService->countMinutesBetweenDates($calendarEvent->date_start, date('Y-m-d H:i'));

        if ($diffTime <= 180) {
            return $this->calendarService->getError(1);
        }

        $updatedField = $this->calendarService->setDate($updateRequest->all(), $calendarEvent);
        if ($updatedField['duration'] <= 0) {
            return $this->calendarService->getError(2);
        }

        Calendar::where('id', $id)->update($updatedField);

        return new CalendarResource(Calendar::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $destroyRequest
     * @param int $id
     * @return String
     */
    public function destroy(DestroyRequest $destroyRequest,$id)
    {
        $calendarEvent = Calendar::find($id);
        $diffTime = $this->calendarService->countMinutesBetweenDates($calendarEvent->date_start, date('Y-m-d H:i'));

        if ($diffTime <= 180) {
            return $this->calendarService->getError(3);
        }
        Calendar::destroy([$id]);

        return json_encode(['success' => 'You have successfully deleted the entry from the ID ' . $id]);

    }

}
