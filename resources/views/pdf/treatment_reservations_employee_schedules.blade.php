<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page {
            margin: 10px;
        }

        header {
            position: fixed;
            top: 0px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            text-transform: uppercase;
            padding-top: 4px;
        }

        .table {
            margin-top: 50px;
            width: 100%;
        }

        .column {
            float: left;
            width: 50%;
            margin-top: 32px;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        tr {
            border-right: 2px solid black;
        }

        td:first-child,
        th:first-child {
            border-left: 2px solid black;
            border-right: 2px solid black;
            border-bottom: 2px solid black;
        }

        td:last-child,
        th:last-child {
            border-right: 2px solid black;
        }

        tr:first-child th {
            border-top: 2px solid black;
            border-bottom: 2px solid black;
        }

        tr:last-child td {
            border-bottom: 2px solid black !important;
        }

        table,
        thead,
        th,
        td {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 18px;
            border: 1px rgb(211, 209, 209) solid;
            border-collapse: collapse;
            vertical-align: middle !important;
        }

        th {
            font-size: 20px;
        }

        td div.first-column {
            min-height: 25px;
        }

        .markReservation {
            background-color: rgb(211, 209, 209);
            padding: 5px;
            vertical-align: top !important;
        }

        .centerContent {
            display: flex;
            align-items: center;
            justify-content: center
        }

        .border-start {
            border-top: 2px black solid;
        }

        .border-end {
            border-bottom: 2px black solid;
        }

        .markNote {
            display: block;
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>

</head>

<body>
    <header>
        {{ $data['date'] }}
    </header>

    @php($count = 2)
    @php($start = 0)
    @php($end = 7)
    @php($continueTd = [])
    @php($countSchedules = count($data['schedules']))
    @php($duration = 0)
    @php($previousUnavailable = false)
    @php($prevEmpty = null)
    @php($prevNoteTimeOff = null)
    <table class="table">
        <thead>
            <tr>
                <th>{{ $data['day'] }}</th>
                <th>
                    {{ $data['employee']->first_name . ' ' . $data['employee']->last_name }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['schedules'] as $blockIndex => $item)
                <?php
                $scheduledStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $data['currentDate']->toDateString() . ' ' . $item['start']);
                $scheduledEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $data['currentDate']->toDateString() . ' ' . $item['end']);
                ?>
                <tr>
                    <td style="width: 15%;">
                        <div class="first-column">
                            <div style="margin-top: 8px; margin-left: 3px">{{ $item['name'] }}</div>
                        </div>
                    </td>

                    @php($dataTd = null)
                    @php($markTimeOff = false)

                    @foreach ($data['employeeWorkingHours']->work_schedule as $workingHours)
                        <?php
                        $startHour = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $data['employeeWorkingHours']->date . ' ' . $workingHours['start']);
                        $endHour = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $data['employeeWorkingHours']->date . ' ' . $workingHours['end']);
                        
                        $endHour->sub(\Carbon\CarbonInterval::minute());
                        $scheduledEnd->sub(\Carbon\CarbonInterval::minute());
                        
                        if ($scheduledStart->between($startHour, $endHour) && $scheduledEnd->between($startHour, $endHour)) {
                            $markTimeOff = false;
                            break;
                        } else {
                            $markTimeOff = true;
                            $noteTimeOff = 'Horas no disponibles';
                        }
                        ?>
                    @endforeach

                    @foreach ($data['employeesTimeOff'] as $timeOff)
                        <?php
                        if ($timeOff->type === 'Día Completo') {
                            $markTimeOff = true;
                            $noteTimeOff = $timeOff->notes;
                        } else {
                            $dayCurrent = date('Y-m-d', strtotime($timeOff->from_date));
                            $from_date = strtotime($timeOff->from_date);
                            $to_date = strtotime($timeOff->to_date);
                            $itemSchedule = strtotime("{$dayCurrent} {$item['start']}:00");
                            if ($from_date <= $itemSchedule && $to_date > $itemSchedule) {
                                $markTimeOff = true;
                                $noteTimeOff = $timeOff->notes;
                            }
                        }
                        ?>
                    @endforeach

                    @php($markNote = null)

                    @foreach ($data['treatmentScheduleNotes'] as $note)
                        <?php
                        $dayCurrent = date('Y-m-d', strtotime($note->date));
                        $from_date = strtotime("{$dayCurrent} {$note->from_hour}:00");
                        $to_date = strtotime("{$dayCurrent} {$note->to_hour}:00");
                        $itemSchedule = strtotime("{$dayCurrent} {$item['start']}:00");
                        if ($from_date <= $itemSchedule && $to_date > $itemSchedule) {
                            $markNote = $note->note;
                        }
                        ?>
                    @endforeach

                    @if ($duration > 0)
                        <?php
                        $addBorderEnd = $duration > 1 ? false : true;
                        ?>
                        <td style="width: 85%" class="markReservation {{ $addBorderEnd ? 'border-end' : '' }}">
                            {{-- @dd($duration, $dataTd, $continueTd, $record) --}}
                            @if ($markTimeOff)
                                {{ !empty($noteTimeOff) ? $noteTimeOff : 'No disponible' }}
                            @endif
                            @if ($markNote)
                                <div class="markNote">
                                    {{ $markNote }}
                                </div>
                            @endif
                        </td>
                        @php($duration--)
                    @else
                        @foreach ($data['records'] as $key => $record)
                            @if (isset($continueTd[$data['employee']->id]) && $continueTd[$data['employee']->id]['id'] == $data['employee']->id)
                                <?php
                                $dataTd['client'] = $continueTd[$data['employee']->id]['td']['client'];
                                $dataTd['phone'] = $continueTd[$data['employee']->id]['td']['phone'];
                                $dataTd['notes'] = $continueTd[$data['employee']->id]['td']['notes'];
                                $duration = $continueTd[$data['employee']->id]['duration'];
                                unset($continueTd[$data['employee']->id]);
                                $dataTd = null;
                                $duration = 0;
                                ?>
                                @php($previousUnavailable = false)
                            @elseif ($record->time == $item['start'] && $record->employee_id == $data['employee']->id)
                                <?php
                                $dataTd['client'] = $record->client->name;
                                $dataTd['phone'] = $record->client->phone;
                                $dataTd['notes'] = preg_split("/[\n]+/", $record->notes)[0];
                                $duration = $record->duration / 30;
                                ?>
                            @endif
                        @endforeach
                        @if ($dataTd)
                            <td class="markReservation border-start {{ $duration === 1 ? 'border-end' : '' }}">
                                <div>{{ $dataTd['client'] }} - {{ $dataTd['notes'] }}</div>
                            </td>
                            @php($duration--)
                            @if (($duration > 0) & $blockIndex)
                                <?php
                                $continueTd[$data['employee']->id]['id'] = $data['employee']->id;
                                $continueTd[$data['employee']->id]['td'] = $dataTd;
                                $continueTd[$data['employee']->id]['duration'] = $duration;
                                ?>
                            @endif
                            @php($previousUnavailable = false)
                            @php($prevNoteTimeOff = null)
                            @php($prevEmpty = false)
                        @else
                            @if ($markTimeOff)
                                @if ((!$previousUnavailable || $prevEmpty) && $noteTimeOff == 'Horas no disponibles')
                                    @php($previousUnavailable = true)
                                    <td class="markReservation border-start">
                                        {{ $noteTimeOff }}
                                    </td>
                                @elseif ($previousUnavailable && $noteTimeOff == 'Horas no disponibles')
                                    @php($previousUnavailable = true)
                                    <td class="markReservation"></td>
                                @elseif ($noteTimeOff != 'Horas no disponibles')
                                    @php($previousUnavailable = false)
                                    <td
                                        class="markReservation{{ $prevNoteTimeOff != $noteTimeOff ? ' border-start' : '' }}">
                                        {{ $prevNoteTimeOff != $noteTimeOff ? $noteTimeOff : '' }}
                                    </td>
                                @endif
                                @php($prevNoteTimeOff = $noteTimeOff)
                                @php($prevEmpty = false)
                            @elseif ($markNote)
                                <td class="markReservation border-start">
                                    <div class="markNote">
                                        {{ $markNote }}
                                    </div>
                                </td>
                                @php($previousUnavailable = false)
                                @php($prevEmpty = false)
                            @else
                                <td class="{{ !$prevEmpty ? ' border-start' : '' }}"
                                    style="{{ $prevEmpty ? 'border-top: none; border-bottom: none' : 'border-bottom: none' }}">
                                </td>
                                @php($prevEmpty = true)
                            @endif
                        @endif
                    @endif
                </tr>
            @endforeach

        </tbody>
    </table>

</body>
