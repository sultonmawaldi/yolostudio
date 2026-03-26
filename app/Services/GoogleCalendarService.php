<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Carbon\Carbon;

class GoogleCalendarService
{
    protected Google_Client $client;
    protected Google_Service_Calendar $calendarService;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Yolo Studio Booking System');

        $this->client->setAuthConfig(
            storage_path('app/google-calendar/yolo-studio-calendar.json')
        );

        // Scope FULL calendar
        $this->client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);

        $this->calendarService = new Google_Service_Calendar($this->client);
    }

    /**
     * Buat event ke Google Calendar cabang
     *
     * @param  \App\Models\Appointment  $appointment
     * @param  string  $calendarId  (xxxx@group.calendar.google.com)
     */
    public function createEvent($appointment, string $calendarId)
    {
        if (empty($calendarId)) {
            throw new \Exception('Calendar ID studio belum diatur');
        }

        if (!$appointment->studio) {
            throw new \Exception('Appointment belum terhubung ke studio');
        }

        $start = Carbon::parse(
            $appointment->booking_date . ' ' . $appointment->booking_start_time,
            config('app.timezone')
        );

        $end = Carbon::parse(
            $appointment->booking_date . ' ' . $appointment->booking_end_time,
            config('app.timezone')
        );

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Booking – ' . $appointment->name,
            'description' =>
            "Studio: {$appointment->studio->name}\n" .
                "Layanan: " . optional($appointment->service)->title . "\n" .
                "Customer: {$appointment->name}\n" .
                "Phone: {$appointment->phone}",

            'start' => [
                'dateTime' => $start->toRfc3339String(),
                'timeZone' => 'Asia/Jakarta',
            ],
            'end' => [
                'dateTime' => $end->toRfc3339String(),
                'timeZone' => 'Asia/Jakarta',
            ],

            // ❌ JANGAN TAMBAHKAN attendees
            // ❌ JANGAN sendUpdates
        ]);

        return $this->calendarService
            ->events
            ->insert($calendarId, $event);
    }
    public function updateEvent($appointment, string $calendarId, string $eventId)
    {
        if (empty($calendarId)) {
            throw new \Exception('Calendar ID tidak ditemukan');
        }

        if (empty($eventId)) {
            throw new \Exception('Google Event ID tidak ditemukan');
        }

        $start = Carbon::parse(
            $appointment->booking_date . ' ' . $appointment->booking_start_time,
            config('app.timezone')
        );

        $end = Carbon::parse(
            $appointment->booking_date . ' ' . $appointment->booking_end_time,
            config('app.timezone')
        );

        // 🔥 Ambil event lama
        $event = $this->calendarService
            ->events
            ->get($calendarId, $eventId);

        // 🔥 Update waktu
        $event->setStart(new \Google_Service_Calendar_EventDateTime([
            'dateTime' => $start->toRfc3339String(),
            'timeZone' => 'Asia/Jakarta',
        ]));

        $event->setEnd(new \Google_Service_Calendar_EventDateTime([
            'dateTime' => $end->toRfc3339String(),
            'timeZone' => 'Asia/Jakarta',
        ]));

        // 🔥 Simpan perubahan
        return $this->calendarService
            ->events
            ->update($calendarId, $eventId, $event);
    }
}
