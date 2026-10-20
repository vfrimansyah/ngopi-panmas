<?php

namespace App\Controllers;

use App\Models\EventModel;

class EventController extends BaseController
{
    protected $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
    }

    public function index()
    {
        $filter = $this->request->getGet('status') ?? 'all';

        $builder = $this->eventModel;

        if ($filter !== 'all') {
            $builder = $builder->where('status', $filter);
        }

        $data = [
            'title' => 'Agenda Event — Ngobrol Pikiran Indonesia',
            'events' => $builder->orderBy('tanggal_mulai', 'DESC')->paginate(9),
            'pager' => $this->eventModel->pager,
            'selectedFilter' => $filter,
        ];

        return view('pages/events/index', $data);
    }

    public function show($slug = null)
    {
        $event = $this->eventModel->where('slug', $slug)->first();

        if (!$event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $event['judul'] . ' — Ngobrol Pikiran Indonesia',
            'event' => $event,
            'upcomingEvents' => $this->eventModel->where('status', 'upcoming')
                                                  ->where('id !=', $event['id'])
                                                  ->orderBy('tanggal_mulai', 'ASC')
                                                  ->limit(3)
                                                  ->find(),
        ];

        return view('pages/events/detail', $data);
    }
}
