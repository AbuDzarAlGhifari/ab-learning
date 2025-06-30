<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\{User, Role, Course, Schedule, Enrollment, Material, Quiz, Question, Choice, Payment, QuizAttempt, Answer};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Roles
        $roles = [
            'admin' => Role::firstOrCreate(['name' => 'admin']),
            'teacher' => Role::firstOrCreate(['name' => 'teacher']),
            'student' => Role::firstOrCreate(['name' => 'student']),
            'finance' => Role::firstOrCreate(['name' => 'finance']),
        ];

        // Users
        $admin = User::firstOrCreate([
            'email' => 'admin@mail.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role_id' => $roles['admin']->id
        ]);

        $finance = User::firstOrCreate([
            'email' => 'finance@mail.com',
        ], [
            'name' => 'Finance',
            'password' => Hash::make('password'),
            'role_id' => $roles['finance']->id
        ]);

        // 3 Teachers
        $teachers = User::factory()->count(3)->create([
            'role_id' => $roles['teacher']->id
        ]);

        // 10 Students
        $students = User::factory()->count(10)->create([
            'role_id' => $roles['student']->id
        ]);

        // 5 Courses
        foreach (range(1, 5) as $i) {
            $course = Course::create([
                'title' => "Course $i - " . $faker->word(),
                'description' => $faker->sentence(10),
            ]);

            $teacher = $teachers->random();

            // Jadwal
            Schedule::create([
                'course_id' => $course->id,
                'teacher_id' => $teacher->id,
                'start_time' => now()->addDays($i),
                'end_time' => now()->addDays($i)->addHours(2),
            ]);

            // Materi
            Material::create([
                'course_id' => $course->id,
                'title' => 'Intro ' . $faker->word(),
                'content' => $faker->paragraph(),
                'order' => 1
            ]);

            // Quiz
            $quiz = Quiz::create([
                'course_id' => $course->id,
                'title' => 'Quiz #' . $i,
                'description' => 'Test materi ' . $course->title,
                'created_by' => $teacher->id
            ]);

            // 2 Questions per quiz
            foreach (range(1, 2) as $j) {
                $type = $j === 2 ? 'essay' : 'multiple_choice';
                $q = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $faker->sentence(),
                    'type' => $type,
                    'score' => 10
                ]);

                if ($type === 'multiple_choice') {
                    foreach (range(1, 3) as $k) {
                        Choice::create([
                            'question_id' => $q->id,
                            'choice_text' => $faker->word(),
                            'is_correct' => $k === 1 // jawaban pertama benar
                        ]);
                    }
                }
            }

            // Enroll 3 random students
            $enrolledStudents = $students->random(3);
            foreach ($enrolledStudents as $student) {
                $enrollment = Enrollment::create([
                    'course_id' => $course->id,
                    'student_id' => $student->id
                ]);

                Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'amount' => 100000,
                    'method' => 'manual',
                    'status' => 'paid',
                    'proof_url' => 'https://dummyimage.com/600x400/000/fff',
                    'confirmed_by' => $finance->id,
                    'confirmed_at' => now()
                ]);

                // Attempt quiz
                $attempt = QuizAttempt::create([
                    'quiz_id' => $quiz->id,
                    'student_id' => $student->id,
                    'started_at' => now()->subMinutes(10),
                    'completed_at' => now(),
                    'score' => 15
                ]);

                // Create dummy answers
                foreach ($quiz->questions as $question) {
                    Answer::create([
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'answer_text' => $question->type === 'essay' ? $faker->sentence(5) : 1,
                        'is_correct' => $question->type === 'multiple_choice' ? true : null,
                        'score' => $question->type === 'essay' ? null : 10,
                    ]);
                }
            }
        }
    }
}
