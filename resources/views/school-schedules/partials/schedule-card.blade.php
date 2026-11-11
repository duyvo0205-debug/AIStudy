<div class="schedule-card">

    <div class="schedule-card-title">

        {{ $schedule->subject_name }}

    </div>


    @if(!empty($schedule->course_code))

        <div class="schedule-card-info">

            <strong>Mã HP:</strong>

            {{ $schedule->course_code }}

        </div>

    @endif


    @if(!empty($schedule->class_code))

        <div class="schedule-card-info">

            <strong>Lớp:</strong>

            {{ $schedule->class_code }}

        </div>

    @endif


    <div class="schedule-card-info">

        <strong>Tiết:</strong>

        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}

        -

        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}

    </div>


    @if(!empty($schedule->room))

        <div class="schedule-card-info">

            <strong>Phòng:</strong>

            {{ $schedule->room }}

        </div>

    @endif


    @if(!empty($schedule->instructor))

        <div class="schedule-card-info">

            <strong>GV:</strong>

            {{ $schedule->instructor }}

        </div>

    @endif


    @if(!empty($schedule->start_date) && !empty($schedule->end_date))

        <div class="schedule-card-info">

            <strong>Thời gian:</strong>

            {{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }}

            -

            {{ \Carbon\Carbon::parse($schedule->end_date)->format('d/m/Y') }}

        </div>

    @endif


    <div class="schedule-card-actions">

        <a href="{{ route(
            'school-schedules.edit',
            ['schedule' => $schedule->id]
        ) }}"
           class="btn btn-warning">

            Sửa

        </a>


        <form action="{{ route(
            'school-schedules.destroy',
            ['schedule' => $schedule->id]
        ) }}"
              method="POST"
              onsubmit="return confirm('Bạn có chắc muốn xóa lịch học này không?')">

            @csrf

            @method('DELETE')

            <button type="submit"
                    class="btn btn-danger">

                Xóa

            </button>

        </form>

    </div>

</div>