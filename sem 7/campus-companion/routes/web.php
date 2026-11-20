<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public Routes

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Role-Based Redirect (Main Dashboard Entry)

Route::middleware(['auth'])->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect('/admin/dashboard'),
        'teacher' => redirect('/teacher/dashboard'),
        'timetable_commity' => redirect('/admin/timetable'),
        default => redirect('/student/dashboard'),
    };
})->name('dashboard');

// Settings (Default Volt)

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::post('/notifications/mark-as-read', function () {
        \App\Models\Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    })->name('notifications.mark-as-read');
});

// ADMIN ROUTES

// SHARED ROUTES (ADMIN & TIMETABLE COMMITTEE)
Route::middleware(['auth', 'role:admin,timetable_commity'])->prefix('admin')->group(function () {
    // Timetable
    Volt::route('/timetable', 'admin.timetable.index');
    Volt::route('/timetable/master', 'admin.timetable.master');
    Volt::route('/timetable/import', 'admin.timetable.import');
    Volt::route('/timetable/create', 'admin.timetable.create');
    Volt::route('/timetable/edit/{id}', 'admin.timetable.edit');

    // Classes
    Volt::route('/classes', 'admin.classes.index');
    Volt::route('/classes/create', 'admin.classes.create');
    Volt::route('/classes/edit/{id}', 'admin.classes.edit');

    // Campus Map & Floors
    Volt::route('/map', 'admin.map');
});

// ADMIN-ONLY ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // Dashboard
    Volt::route('/dashboard', 'admin.dashboard');

    // Notices (CRUD)
    Volt::route('/notices', 'admin.notices.index');
    Volt::route('/notices/create', 'admin.notices.create');
    Volt::route('/notices/edit/{id}', 'admin.notices.edit');

    // Events
    Volt::route('/events', 'admin.events.index');
    Volt::route('/events/create', 'admin.events.create');
    Volt::route('/events/edit/{id}', 'admin.events.edit');

    // Users
    Volt::route('/users', 'admin.users.index');
    Volt::route('/users/create', 'admin.users.create');
    Volt::route('/users/edit/{id}', 'admin.users.edit');

    // Department
    Volt::route('/departments', 'admin.departments.index');
    Volt::route('/departments/create', 'admin.departments.create');
    Volt::route('/departments/edit/{id}', 'admin.departments.edit');

    // Complaints
    Volt::route('/complaints', 'admin.complaints.index');

});

// TEACHER ROUTES

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {

    Volt::route('/dashboard', 'teacher.dashboard');

    // Notices (view only)
    Volt::route('/notices', 'teacher.notices.index');
    Volt::route('/notices/create', 'teacher.notices.create');

    // Events
    Volt::route('/events', 'teacher.events.index');

    // Timetable
    Volt::route('/timetable', 'teacher.timetable.index');

    // Complaints
    Volt::route('/complaints', 'teacher.complaints.index');

    Volt::route('/profile', 'teacher.profile');

    // Map
    Volt::route('/map', 'teacher.map');
});

// STUDENT ROUTES

Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {

    Volt::route('/dashboard', 'student.dashboard');

    // Notices
    Volt::route('/notices', 'student.notices.index');

    // Events
    Volt::route('/events', 'student.events.index');

    // Timetable
    Volt::route('/timetable', 'student.timetable.index');

    // Profile
    Volt::route('/profile', 'student.profile');

    // Complaints
    Volt::route('/complaints', 'student.complaints.index');

    // Map
    Volt::route('/map', 'student.map');
});

// Auth Routes

Route::post('/login', function (Request $request) {

    if ($request->has('email')) {
        $cleanedEmail = trim(str_replace(['"', "'", '`', ' '], '', $request->input('email')));
        $request->merge(['email' => $cleanedEmail]);
    }

    $credentials = $request->validate([
        'email' => ['required', 'string'],
        'password' => ['required'],
    ]);

    $remember = $request->boolean('remember');

    if (! Auth::attempt($credentials, $remember)) {
        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->withInput();
    }

    $request->session()->regenerate();

    $user = auth()->user();

    return redirect()->intended(match ($user->role) {
        'admin' => '/admin/dashboard',
        'teacher' => '/teacher/dashboard',
        'timetable_commity' => '/admin/timetable',
        default => '/student/dashboard',
    });

})->name('login.post');

// Safe file serving route for uploads and public storage
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (! file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.local');

require __DIR__.'/auth.php';
