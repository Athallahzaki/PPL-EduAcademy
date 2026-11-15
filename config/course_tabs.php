<?php

return [
    'instructor' => [
        [
            'label' => 'Overview',
            'route' => 'instructor.courses.show',
            'active' => 'instructor.courses.show',
        ],
        [
            'label' => 'Students',
            'route' => 'instructor.courses.students',
            'active' => 'instructor.courses.students',
        ],
        [
            'label' => 'Modules',
            'route' => 'instructor.courses.modules.index',
            'active' => 'instructor.courses.modules.*',
        ], // Reserved for PBI-12
        [
            'label' => 'Assignments',
            'route' => 'instructor.courses.assignments.index',
            'active' => 'instructor.courses.assignments.*',
        ], // Reserved for PBI-13
        [
            'label' => 'Live Sessions',
            'route' => 'instructor.courses.sessions.index',
            'active' => 'instructor.courses.sessions.*',
        ], // Reserved for PBI-05
        [
            'label' => 'Quizzes',
            'route' => 'instructor.courses.quizzes.index',
            'active' => 'instructor.courses.quizzes.*',
        ], // Reserved for PBI-06
        [
            'label' => 'Reviews',
            'route' => 'instructor.courses.reviews.index',
            'active' => 'instructor.courses.reviews.*',
        ], // Reserved for PBI-09
    ],

    'student' => [
        [
            'label' => 'Overview',
            'route' => 'student.courses.show',
            'active' => 'student.courses.show',
        ],
        [
            'label' => 'Content',
            'route' => 'student.courses.modules.index',
            'active' => 'student.courses.modules.*',
        ], // Reserved for PBI-12
        [
            'label' => 'Assignments',
            'route' => 'student.courses.assignments.index',
            'active' => 'student.courses.assignments.*',
        ], // Reserved for PBI-13
        [
            'label' => 'Live Sessions',
            'route' => 'student.courses.sessions.index',
            'active' => 'student.courses.sessions.*',
        ], // Reserved for PBI-05
        [
            'label' => 'Quizzes',
            'route' => 'student.courses.quizzes.index',
            'active' => 'student.courses.quizzes.*',
        ], // Reserved for PBI-06
        [
            'label' => 'Reviews',
            'route' => 'student.courses.reviews.index',
            'active' => 'student.courses.reviews.*',
        ], // Reserved for PBI-09
    ],
];
