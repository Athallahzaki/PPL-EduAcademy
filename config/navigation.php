<?php

return [
    'roles' => [
        'admin' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
                'icon' => 'home',
            ],
            [
                'label' => 'Manage Accounts & Roles',
                'route' => 'admin.users.index',
                'active' => 'admin.users.*',
                'icon' => 'users',
            ],
            [
                'label' => 'System Announcements',
                'route' => 'admin.announcements.index',
                'active' => 'admin.announcements.*',
                'icon' => 'speakerphone',
            ],
            [
                'label' => 'Scholarship Info',
                'route' => 'admin.scholarships.index',
                'active' => 'admin.scholarships.*',
                'icon' => 'academic-cap',
            ],
            [
                'label' => 'Scholarship Validation',
                'route' => 'admin.scholarship-validations.index',
                'active' => 'admin.scholarship-validations.*',
                'icon' => 'clipboard-check',
            ],
            [
                'label' => 'Events & Webinars',
                'route' => 'admin.events.index',
                'active' => 'admin.events.*',
                'icon' => 'calendar',
            ],
            [
                'label' => 'Forum Moderation',
                'route' => 'admin.forums.index',
                'active' => 'admin.forums.*',
                'icon' => 'chat-alt-2',
            ],
            [
                'label' => 'Reviews & Ratings',
                'route' => 'admin.reviews.index',
                'active' => 'admin.reviews.*',
                'icon' => 'star',
            ],
            [
                'label' => 'FAQ',
                'route' => 'admin.faqs.index',
                'active' => 'admin.faqs.*',
                'icon' => 'question-mark-circle',
            ],
        ],

        'student' => [
            [
                'label' => 'Dashboard',
                'route' => 'student.dashboard',
                'active' => 'student.dashboard',
                'icon' => 'home',
            ],
            [
                'label' => 'Class Catalog',
                'route' => 'student.classes.index',
                'active' => 'student.classes.*',
                'icon' => 'book-open',
            ],
            [
                'label' => 'My Classes',
                'route' => 'student.my-classes.index',
                'active' => 'student.my-classes.*',
                'icon' => 'collection',
            ],
            [
                'label' => 'Assignments & Quizzes',
                'route' => 'student.assignments.index',
                'active' => 'student.assignments.*',
                'icon' => 'clipboard-list',
            ],
            [
                'label' => 'Discussion Forum',
                'route' => 'student.forums.index',
                'active' => 'student.forums.*',
                'icon' => 'chat-alt-2',
            ],
            [
                'label' => 'Scholarships',
                'route' => 'student.scholarships.index',
                'active' => 'student.scholarships.*',
                'icon' => 'academic-cap',
            ],
            [
                'label' => 'Events & Webinars',
                'route' => 'student.events.index',
                'active' => 'student.events.*',
                'icon' => 'calendar',
            ],
            [
                'label' => 'Profile',
                'route' => 'student.profile.show',
                'active' => 'student.profile.*',
                'icon' => 'user-circle',
            ],
        ],

        'instructor' => [
            [
                'label' => 'Dashboard',
                'route' => 'instructor.dashboard',
                'active' => 'instructor.dashboard',
                'icon' => 'home',
            ],
            [
                'label' => 'Profile',
                'route' => 'instructor.profile.show',
                'active' => 'instructor.profile.*',
                'icon' => 'user-circle',
            ],
            [
                'label' => 'My Classes',
                'route' => 'instructor.classes.index',
                'active' => 'instructor.classes.*',
                'icon' => 'book-open',
            ],
            [
                'label' => 'Modules & Chapters',
                'route' => 'instructor.modules.index',
                'active' => 'instructor.modules.*',
                'icon' => 'collection',
            ],
            [
                'label' => 'Assignments',
                'route' => 'instructor.assignments.index',
                'active' => 'instructor.assignments.*',
                'icon' => 'clipboard-list',
            ],
            [
                'label' => 'Live Sessions',
                'route' => 'instructor.sessions.index',
                'active' => 'instructor.sessions.*',
                'icon' => 'video-camera',
            ],
            [
                'label' => 'Quizzes',
                'route' => 'instructor.quizzes.index',
                'active' => 'instructor.quizzes.*',
                'icon' => 'document-text',
            ],
            [
                'label' => 'Grade Summaries',
                'route' => 'instructor.grades.index',
                'active' => 'instructor.grades.*',
                'icon' => 'chart-bar',
            ],
            [
                'label' => 'Reviews & Ratings',
                'route' => 'instructor.reviews.index',
                'active' => 'instructor.reviews.*',
                'icon' => 'star',
            ],
            [
                'label' => 'Discussion Forum',
                'route' => 'instructor.forums.index',
                'active' => 'instructor.forums.*',
                'icon' => 'chat-alt-2',
            ],
        ],
    ],

    'help_route' => 'help',
];

