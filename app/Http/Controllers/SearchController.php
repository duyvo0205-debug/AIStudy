<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Tìm kiếm toàn bộ dữ liệu AIStudy.
     */
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $keyword = trim($request->input('q'));

        // Chuẩn hóa từ khóa để tìm kiếm không dấu.
        $normalizedKeyword = $this->normalize($keyword);

        $userId = auth()->id();

        $results = [];

        /*
        |--------------------------------------------------------------------------
        | 1. Tìm kiếm bài học và tài liệu
        |--------------------------------------------------------------------------
        */

        $this->searchTable(
            $results,
            'learning_contents',
            [
                'id',
                'title',
                'description',
                'content',
                'subject',
            ],
            [
                'title',
                'description',
                'content',
                'subject',
            ],
            'title',
            'subject',
            'Bài học',
            'book-open',
            $userId,
            $normalizedKeyword,
            fn ($id) => route('learning.show', $id)
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Tìm kiếm bài tập
        |--------------------------------------------------------------------------
        */

        $this->searchTable(
            $results,
            'exercises',
            [
                'id',
                'title',
                'question',
                'explanation',
                'difficulty',
            ],
            [
                'title',
                'question',
                'explanation',
                'difficulty',
            ],
            'title',
            'difficulty',
            'Bài tập',
            'clipboard-list',
            $userId,
            $normalizedKeyword,
            fn ($id) => route('exercises.show', $id)
        );

        /*
        |--------------------------------------------------------------------------
        | 3. Tìm kiếm mục tiêu học tập
        |--------------------------------------------------------------------------
        */

        $this->searchTable(
            $results,
            'learning_goals',
            [
                'id',
                'title',
                'description',
                'subject',
                'current_level',
                'target_level',
            ],
            [
                'title',
                'description',
                'subject',
                'current_level',
                'target_level',
            ],
            'title',
            'subject',
            'Mục tiêu',
            'target',
            $userId,
            $normalizedKeyword,
            fn ($id) => route('learning-goals.index')
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Tìm kiếm lịch tự học
        |--------------------------------------------------------------------------
        */

        $this->searchTable(
            $results,
            'study_schedules',
            [
                'id',
                'title',
                'subject',
                'learning_goal',
                'study_content',
                'description',
            ],
            [
                'title',
                'subject',
                'learning_goal',
                'study_content',
                'description',
            ],
            'title',
            'subject',
            'Lịch tự học',
            'calendar',
            $userId,
            $normalizedKeyword,
            fn ($id) => route('study-schedules.index')
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Tìm kiếm thời khóa biểu
        |--------------------------------------------------------------------------
        */

        $this->searchTable(
            $results,
            'school_schedules',
            [
                'id',
                'subject_name',
                'course_code',
                'class_code',
                'room',
                'instructor',
            ],
            [
                'subject_name',
                'course_code',
                'class_code',
                'room',
                'instructor',
            ],
            'subject_name',
            'course_code',
            'Thời khóa biểu',
            'calendar-days',
            $userId,
            $normalizedKeyword,
            fn ($id) => route('school-schedules.index')
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Tìm kiếm nhanh các trang chức năng
        |--------------------------------------------------------------------------
        */

        $pages = [
            [
                'title' => 'Tổng quan',
                'description' => 'Trang tổng quan học tập',
                'keywords' => 'dashboard tong quan trang chu',
                'type' => 'Trang chức năng',
                'icon' => 'house',
                'url' => route('dashboard'),
            ],
            [
                'title' => 'Trợ lý AI',
                'description' => 'Chat và hỏi đáp với AI',
                'keywords' => 'tro ly ai chat chatbot hoi dap',
                'type' => 'Trang chức năng',
                'icon' => 'sparkles',
                'url' => route('ai-assistant.index'),
            ],
            [
                'title' => 'Lộ trình cá nhân',
                'description' => 'Xem lộ trình học tập cá nhân',
                'keywords' => 'lo trinh ca nhan hoc tap',
                'type' => 'Trang chức năng',
                'icon' => 'route',
                'url' => route('learning-path.index'),
            ],
            [
                'title' => 'Đề xuất của AI',
                'description' => 'Xem đề xuất học tập từ AI',
                'keywords' => 'de xuat ai goi y hoc tap',
                'type' => 'Trang chức năng',
                'icon' => 'lightbulb',
                'url' => route('recommendations.index'),
            ],
            [
                'title' => 'Tiến độ học tập',
                'description' => 'Theo dõi kết quả học tập',
                'keywords' => 'tien do ket qua thong ke',
                'type' => 'Trang chức năng',
                'icon' => 'chart-no-axes-combined',
                'url' => route('progress.index'),
            ],
            [
                'title' => 'Hồ sơ cá nhân',
                'description' => 'Thông tin tài khoản',
                'keywords' => 'ho so ca nhan tai khoan',
                'type' => 'Trang chức năng',
                'icon' => 'user',
                'url' => route('profile'),
            ],
            [
                'title' => 'Mục tiêu học tập',
                'description' => 'Quản lý mục tiêu học tập',
                'keywords' => 'muc tieu hoc tap',
                'type' => 'Trang chức năng',
                'icon' => 'target',
                'url' => route('learning-goals.index'),
            ],
            [
                'title' => 'Lịch tự học',
                'description' => 'Quản lý lịch tự học',
                'keywords' => 'lich tu hoc',
                'type' => 'Trang chức năng',
                'icon' => 'calendar',
                'url' => route('study-schedules.index'),
            ],
            [
                'title' => 'Thời khóa biểu',
                'description' => 'Quản lý thời khóa biểu',
                'keywords' => 'thoi khoa bieu lich hoc tren truong',
                'type' => 'Trang chức năng',
                'icon' => 'calendar-days',
                'url' => route('school-schedules.index'),
            ],
            [
                'title' => 'Bài tập',
                'description' => 'Quản lý và luyện tập bài tập',
                'keywords' => 'bai tap luyen tap',
                'type' => 'Trang chức năng',
                'icon' => 'clipboard-list',
                'url' => route('exercises.index'),
            ],
            [
                'title' => 'Học tập',
                'description' => 'Quản lý bài học và tài liệu',
                'keywords' => 'hoc tap bai hoc tai lieu',
                'type' => 'Trang chức năng',
                'icon' => 'book-open',
                'url' => route('learning.index'),
            ],
        ];

        foreach ($pages as $page) {
            $searchText = $this->normalize(
                $page['title'] . ' ' .
                $page['description'] . ' ' .
                $page['keywords']
            );

            if (str_contains($searchText, $normalizedKeyword)) {
                $results[] = $page;
            }
        }

        return response()->json([
            'success' => true,
            'keyword' => $keyword,
            'total' => count($results),
            'results' => array_slice($results, 0, 15),
        ]);
    }

    /**
     * Tìm kiếm dữ liệu trong một bảng.
     */
    private function searchTable(
        array &$results,
        string $table,
        array $columns,
        array $searchFields,
        string $titleField,
        string $subtitleField,
        string $type,
        string $icon,
        int $userId,
        string $keyword,
        callable $urlCallback
    ): void {
        $rows = DB::table($table)
            ->where('user_id', $userId)
            ->get($columns);

        foreach ($rows as $row) {
            $values = [];

            foreach ($searchFields as $field) {
                $values[] = $row->{$field} ?? '';
            }

            $searchText = $this->normalize(
                implode(' ', $values)
            );

            if (!str_contains($searchText, $keyword)) {
                continue;
            }

            $results[] = [
                'title' => $row->{$titleField} ?: 'Không có tiêu đề',
                'description' => $row->{$subtitleField} ?? '',
                'type' => $type,
                'icon' => $icon,
                'url' => $urlCallback($row->id),
            ];
        }
    }

    /**
     * Chuẩn hóa tiếng Việt để hỗ trợ tìm kiếm không dấu.
     */
    private function normalize(string $value): string
    {
        return Str::lower(
            Str::ascii($value)
        );
    }
}
