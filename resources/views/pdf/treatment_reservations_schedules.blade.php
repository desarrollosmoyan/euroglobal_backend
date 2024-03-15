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
            font-size: 8px;
            border: 1px rgb(211, 209, 209) solid;
            padding: 5px 0
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

        th {
            font-weight: bold;
            font-size: 14px;
            /* border: 1px rgb(211, 209, 209) solid; */
            border: 2px black solid;
            border-collapse: collapse;
            vertical-align: middle !important;
        }

        table,
        td {
            text-transform: uppercase;
            font-weight: bold;
            height: 60px !important;
            font-size: 12px;
            border: 2px black solid;
            border-collapse: collapse;
            vertical-align: middle !important;
        }

        .markReservation {
            background-color: rgb(239, 238, 238);
            padding: 5px 0px 20px 5px;
            vertical-align: top !important;
        }

        .centerContent {
            display: flex;
            align-items: center;
            justify-content: center
        }

        .no-border {
            border: 1px solid rgb(211, 209, 209);
        }

        .markNote {
            display: block;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .free {
            border: 2px black solid !important;
        }
    </style>

</head>

<body>
{{-- <header> --}}
{{--    {{$data['date']}} --}}
{{-- </header> --}}

@php($count = 3)
@php($start = 0)
@php($end = 9)
@php($continueTd = [])
@php($pivotTd = null)
@php($countSchedules = count($data['schedules']))
@php($previousUnavailable = false)
@php($prevNote = '')
@php($noteTimeOff = '')

@for ($i = 0; $i < $count; $i++)
    {{--    @if (isset($continueTd['duration'])) --}}
    {{--        @php($continueTd = []) --}}
    {{--    @endif --}}
    @if ($start === $end)
        @break
    @endif
    @if ($i > 0)
        <div style="page-break-before: always;"></div>
    @endif
    @php($initForeach = $start)
    @php($breakPoint = $end)
    <div class="row">
        <table class="table">
            <thead>
            <tr>
                <th width="9%" style="padding:10px; vertical-align: middle; text-align:left">
                    <span>{{ $data['date'] }}</span>
                </th>
                @foreach ($data['schedules'] as $blockIndex => $item)
                    {{--                        @if ($blockIndex > 8) --}}
                    {{--                            @dd([$initForeach, $breakPoint]) --}}
                    {{--                        @endif --}}
                    @if ($blockIndex < $initForeach)
                        @continue
                    @endif
                    @if ($blockIndex === $breakPoint)
                        @break
                    @endif
                    <th style="white-space: nowrap;">
                        <div class="centerContent">
                            {{ $item['start'] }} - {{ $item['end'] }}
                        </div>
                    </th>
                @endforeach

            </tr>
            </thead>
            <tbody>
            @foreach ($data['employees'] as $keyEmp => $employee)
                @php($duration = 0)
                <tr>
                    <td width="5%" style="padding:10px;vertical-align: middle">
                        {{ $employee->first_name . ' ' . $employee->last_name }}
                    </td>
                    @foreach ($data['schedules'] as $blockIndex => $item)

                        <?php
                            $scheduledStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $data['currentDate']->toDateString() . ' ' . $item['start']);
                            $scheduledEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $data['currentDate']->toDateString() . ' ' . $item['end']);
                        ?>

                        @if ($blockIndex < $initForeach)
                            @continue
                        @endif
                        @if ($blockIndex === $breakPoint)
                            @break
                        @endif
                        @php($dataTd = null)
                        @php($markTimeOff = false)
                        @if($employeeWorkingHours = $data['employeesWorkingHours']->getData()->where('employee_id', $employee->id)->first())
                            @foreach($employeeWorkingHours->work_schedule as $workingHours)
                                <?php
                                    $startHour = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $employeeWorkingHours->date . ' ' . $workingHours['start']);
                                    $endHour = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $employeeWorkingHours->date . ' ' . $workingHours['end']);

                                    $endHour->sub(\Carbon\CarbonInterval::minute());
                                    $scheduledEnd->sub(\Carbon\CarbonInterval::minute());

                                    if ($scheduledStart->between($startHour, $endHour) && $scheduledEnd->between($startHour, $endHour)) {
                                        $markTimeOff = false;
                                        break;
                                    }else {
                                        $markTimeOff = true;
                                        $noteTimeOff = 'Horas no disponibles';
                                    }
                                ?>
                            @endforeach
                        @endif
                        @foreach ($data['employeesTimeOff'][$employee->id] as $timeOff)
                                <?php
                                if ($timeOff->type === 'Día Completo') {
                                    $markTimeOff = true;
                                    $noteTimeOff = $timeOff->notes;
                                } else {
                                    $dayCurrent = date('Y-m-d', strtotime($timeOff->from_date));
                                    $from_date = strtotime($timeOff->from_date);
                                    $to_date = strtotime($timeOff->to_date);
                                    $itemSchedule = strtotime("{$dayCurrent} {$item['start']}:00");
                                    // dump($from_date, $to_date, $itemSchedule);
                                    if ($from_date <= $itemSchedule && $to_date > $itemSchedule) {
                                        $markTimeOff = true;
                                        $noteTimeOff = $timeOff->notes;
                                    }
                                }
                                ?>
                        @endforeach


                        @php($markNote = null)
                        @foreach ($data['treatmentScheduleNotes'][$employee->id] as $note)
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
                            @if ($markTimeOff || $markNote)
                            <td width="9%" class="markReservation" style="border-left: none; border-right: none;">
                                @if ($markTimeOff)
                                    {{ !empty($noteTimeOff) ? $noteTimeOff : 'No disponible' }}
                                @endif
                                @if ($markNote)
                                    <div class="markNote">
                                        {{ $markNote }}
                                    </div>
                                @endif
                            </td>
                            @endif
                            @php($duration--)
                            @if (($duration > 0) & ($blockIndex === $breakPoint - 1))
                                    <?php
                                    $continueTd[$employee->id]['id'] = $employee->id;
                                    $continueTd[$employee->id]['duration'] = $duration;
                                    $continueTd[$employee->id]['td'] = $pivotTd;
                                    $pivotTd = null;
                                    ?>
                            @endif
                        @else
                            @foreach ($data['records'] as $key => $record)
                                @if (isset($continueTd[$employee->id]) && $continueTd[$employee->id]['id'] == $employee->id)
                                        <?php
                                        //                                    $dataTd['product_name'] = $continueTd[$employee->id]['td']['product_name'];
                                        $dataTd['client'] = $continueTd[$employee->id]['td']['client'];
                                        $dataTd['phone'] = $continueTd[$employee->id]['td']['phone'];
                                        $dataTd['notes'] = $continueTd[$employee->id]['td']['notes'];
                                        $duration = $continueTd[$employee->id]['duration'];
                                        unset($continueTd[$employee->id]);
                                        ?>
                                @elseif ($record->time == $item['start'] && $record->employee_id == $employee->id)
                                        <?php
                                        //                                        $dataTd['product_name'] = $record->orderDetails[0]['product_name'];
                                        $dataTd['client'] = $record->client->name;
                                        $dataTd['phone'] = $record->client->phone;
                                        $dataTd['notes'] = preg_split("/[\n]+/", $record->notes)[0];
                                        $duration = round($record->duration / 30);
                                        ?>
                                @endif
                            @endforeach
                            @if ($dataTd)
                                @php($pivotTd = $dataTd)
                                <td width="9%" colspan="{{ (($blockIndex + $duration) > $breakPoint ) ? $breakPoint - $blockIndex : $duration }}"
                                    class="markReservation" style="border-right: none">
                                    {{--                                    <div>{{$dataTd['product_name']}}</div> --}}
                                    <div>{{ $dataTd['client'] }}</div>
                                    <div>{{ $dataTd['notes'] }}</div>
                                    @if ($markNote)
                                        <div class="markNote">
                                            {{ $markNote }}
                                        </div>
                                    @endif
                                </td>
                                @php($duration--)
                                @if (($duration > 0) & ($blockIndex === $breakPoint - 1))
                                        <?php
                                        $continueTd[$employee->id]['id'] = $employee->id;
                                        $continueTd[$employee->id]['duration'] = $duration;
                                        $continueTd[$employee->id]['td'] = $dataTd;
                                        ?>
                                @endif
                                @php($prevNote = '')
                            @else
                                <td width="9%"
                                class="{{ ($markTimeOff || $markNote) ? 'markReservation': 'free' }}"
                                style="{{ ($noteTimeOff == $prevNote) ? 'border-left: none; border-right: none' : 'border-right: none' }}"
                                >
                                    @if ($markTimeOff)
                                        @if ($noteTimeOff == $prevNote && in_array($blockIndex, array(0, 9, 18)))
                                            {{ $noteTimeOff }}
                                            @php($prevNote = $noteTimeOff)
                                        @elseif ($noteTimeOff != $prevNote)
                                            {{ $noteTimeOff }}
                                            @php($prevNote = $noteTimeOff)
                                        @endif
                                    @elseif ($markNote)
                                            <div class="markNote">
                                                {{ $markNote }}
                                            </div>
                                    @else
                                        @php($prevNote = '')
                                    @endif
                                </td>
                            @endif
                        @endif
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @php($start = $breakPoint)
    @php($end = $start * 2)
    @if ($end > $countSchedules)
        @php($end = $countSchedules)
    @endif
@endfor

{{--    @if ($countSchedules >= 25) --}}

{{--    <div style="page-break-before: always;"></div> --}}

{{--    @php($nextPos = intval(($countSchedules - $end)/2)) --}}
{{--    @php($start = $end) --}}
{{--    @php($end = $start + $nextPos) --}}
{{--    @php($continueTd = []) --}}

{{--    @for ($i = 0; $i < $count; $i++) --}}
{{--        @php($initForeach = $start) --}}
{{--        @php($breakPoint = $end) --}}
{{--        <div class="row"> --}}
{{--            <table class="table"> --}}
{{--                <thead> --}}
{{--                    <tr> --}}
{{--                        <th width="5%" style="padding:10px; vertical-align: middle; text-align:left"> --}}
{{--                            Tarde --}}
{{--                        </th> --}}
{{--                        @foreach ($data['schedules'] as $blockIndex => $item) --}}
{{--                            @if ($blockIndex < $initForeach) --}}
{{--                                @continue --}}
{{--                            @endif --}}
{{--                            @if ($blockIndex === $breakPoint) --}}
{{--                                @break --}}
{{--                            @endif --}}
{{--                            <th> --}}
{{--                                <div class="centerContent"> --}}
{{--                                    {{$item['start']}} --}}
{{--                                </div> --}}
{{--                            </th> --}}
{{--                        @endforeach --}}

{{--                    </tr> --}}
{{--                </thead> --}}
{{--                <tbody> --}}
{{--                    @foreach ($data['employees'] as $keyEmp => $employee) --}}
{{--                    @php($duration = 0) --}}
{{--                    <tr> --}}
{{--                        <td width="5%" style="padding:10px;vertical-align: middle"> --}}
{{--                            {{$employee->first_name}} --}}
{{--                        </td> --}}
{{--                        @foreach ($data['schedules'] as $blockIndex => $item) --}}
{{--                            @if ($blockIndex < $initForeach) --}}
{{--                                @continue --}}
{{--                            @endif --}}
{{--                            @if ($blockIndex === $breakPoint) --}}
{{--                                @break --}}
{{--                            @endif --}}
{{--                            @php($dataTd = null) --}}
{{--                            @if ($duration > 0) --}}
{{--                                <td width="15%" class="markReservation"></td> --}}
{{--                                @php($duration--) --}}
{{--                            @else --}}
{{--                                @foreach ($data['records'] as $key => $record) --}}
{{--                                    @if (isset($continueTd[$employee->id]) && $continueTd[$employee->id]['id'] == $employee->id) --}}
{{--                                        <?php--}}
{{--                                        $dataTd['product_name'] = $continueTd[$employee->id]['td']['product_name'];--}}
{{--                                        $dataTd['client']       = $continueTd[$employee->id]['td']['client'];--}}
{{--                                        $dataTd['phone']        = $continueTd[$employee->id]['td']['phone'];--}}
{{--                                        $duration = $continueTd[$employee->id]['duration'];--}}
{{--                                        unset($continueTd[$employee->id]);--}}
{{--                                        ?> ?> ?> ?> ?> ?> ?> ?> ?> --}}
{{--                                    @elseif ($record->time == $item['start'] && $record->employee_id == $employee->id) --}}
{{--                                        <?php--}}
{{--                                            $dataTd['product_name'] = $record->orderDetails[0]['product_name'];--}}
{{--                                            $dataTd['client']       = $record->client->first_name . ' ' . $record->client->last_name . ' ' . $record->client->second_last_name;--}}
{{--                                            $dataTd['phone']        = $record->client->phone;--}}
{{--                                            $duration = $record->duration / 30;--}}
{{--                                        ?> ?> ?> ?> ?> ?> ?> ?> ?> --}}
{{--                                    @endif --}}
{{--                                @endforeach --}}
{{--                                @if ($dataTd) --}}
{{--                                    <td width="15%" class="markReservation"> --}}
{{--                                        <div>{{$dataTd['product_name']}}</div> --}}
{{--                                        <div>{{$dataTd['client']}}</div> --}}
{{--                                        <div>{{$dataTd['phone']}}</div> --}}
{{--                                    </td> --}}
{{--                                    @php($duration--) --}}
{{--                                    @if (($duration > 0) & ($blockIndex === $breakPoint - 1)) --}}
{{--                                        <?php--}}
{{--                                        $continueTd[$employee->id]['id'] = $employee->id;--}}
{{--                                        $continueTd[$employee->id]['td'] = $dataTd;--}}
{{--                                        $continueTd[$employee->id]['duration'] = $duration;--}}
{{--                                        ?> ?> ?> ?> ?> ?> ?> ?> ?> --}}
{{--                                    @endif --}}
{{--                                @else --}}
{{--                                    <td width="15%" ></td> --}}
{{--                                @endif --}}
{{--                            @endif --}}
{{--                        @endforeach --}}
{{--                    </tr> --}}
{{--                    @endforeach --}}
{{--                </tbody> --}}
{{--            </table> --}}
{{--        </div> --}}
{{--        @php($start = $breakPoint) --}}
{{--        @php($end = $countSchedules) --}}
{{--    @endfor --}}
{{-- @endif --}}

</body>
