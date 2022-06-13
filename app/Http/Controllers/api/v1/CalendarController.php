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
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{

    /**
     * @var CalendarService
     */
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
    public function list(ShowRequest $showRequest)
    {
        return $this->calendarService->handleList($showRequest->all());
    }

    /**
     * Show the form for creating a new resource.
     * @param CreateRequest $createRequest
     * @return CalendarResource
     */
    public function create(CreateRequest $createRequest)
    {
       return $this->calendarService->createEvent($createRequest->all());
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
        return $this->calendarService->updateEvent($updateRequest->all(),$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $destroyRequest
     * @param int $id
     * @return String|array
     */
    public function destroy(DestroyRequest $destroyRequest,$id)
    {
        return $this->calendarService->destroyEvent($id);
    }

}
