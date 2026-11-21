<x-instructor.course-layout :course="$course" title="Enrolled Students - {{ $course->title }}">
    <div class="space-y-6">
        <!-- Sub-header & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">Enrolled Student Roster</h2>
                <p class="text-xs text-gray-500">
                    Showing students currently enrolled in this course.
                </p>
            </div>

            <!-- Search -->
            <form method="GET" action="{{ route('instructor.courses.students', $course) }}" class="flex items-center gap-2">
                <x-input
                    id="q"
                    type="search"
                    name="q"
                    :value="request('q')"
                    placeholder="Search students..."
                    class="py-1.5 text-xs w-56"
                />
                <x-button type="submit" size="sm">Search</x-button>
                @if(request('q'))
                    <a
                        href="{{ route('instructor.courses.students', $course) }}"
                        class="text-xs text-gray-500 hover:text-gray-900 underline"
                    >
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Students Table -->
        <x-table>
            <x-slot:headers>
                <th scope="col" class="px-6 py-3.5">Student</th>
                <th scope="col" class="px-6 py-3.5">Email</th>
                <th scope="col" class="px-6 py-3.5">Enrolled Date</th>
            </x-slot:headers>

            @forelse ($students as $student)
                <tr class="hover:bg-gray-50/75 transition-colors">
                    <!-- Name -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 font-bold text-xs uppercase">
                                {{ substr($student->name, 0, 1) }}
                            </span>
                            <x-user-name :user="$student" />
                        </div>
                    </td>

                    <!-- Email -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-mono">
                        {{ $student->email }}
                    </td>

                    <!-- Enrolled Date -->
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y H:i') }} WIB
                    </td>
                </tr>
            @empty
                <x-table.empty colspan="3" message="No enrolled students found." description="Students who enroll in this published class will appear here." />
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>
</x-instructor.course-layout>

