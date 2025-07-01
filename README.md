<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Ab-Learning API Documentation

Base URL: `/api`

## Table of Contents

1. [Authentication](#authentication)
2. [Users & Profile](#users--profile)
3. [Courses (Admin)](#courses-admin)
4. [Schedules](#schedules)
5. [Enrollment & Payments (Student / Finance)](#enrollment--payments)
6. [Materials](#materials)
7. [Quizzes & Questions (Teacher)](#quizzes--questions-teacher)
8. [Quiz Flow (Student)](#quiz-flow-student)
9. [Essay Grading (Admin/Teacher)](#essay-grading-adminteacher)
10. [Quiz Statistics (Admin/Teacher)](#quiz-statistics-adminteacher)
11. [Assignments & Submissions](#assignments--submissions)
12. [Notifications](#notifications)
13. [CMS: Landing Page](#cms-landing-page)
14. [Search & Filter](#search--filter)
15. [Dashboard](#dashboard)

---

## Authentication

| Method | Endpoint    | Description                  |
| ------ | ----------- | ---------------------------- |
| POST   | `/login`    | Login, returns Sanctum token |
| POST   | `/register` | Register new user            |
| GET    | `/user`     | Get current user             |
| POST   | `/logout`   | Logout (revoke token)        |

---

## Users & Profile

| Method | Endpoint   | Role | Description                       |
| ------ | ---------- | ---- | --------------------------------- |
| GET    | `/profile` | All  | Get own profile (200 / 204 empty) |
| PATCH  | `/profile` | All  | Update profile fields & avatar    |

---

## Courses (Admin)

| Method | Endpoint            | Description        |
| ------ | ------------------- | ------------------ |
| GET    | `/courses`          | List all courses   |
| POST   | `/courses`          | Create course      |
| GET    | `/courses/{course}` | Get course details |
| PATCH  | `/courses/{course}` | Update course      |
| DELETE | `/courses/{course}` | Delete course      |

---

## Schedules

| Method | Endpoint        | Role    | Description                 |
| ------ | --------------- | ------- | --------------------------- |
| POST   | `/schedules`    | Teacher | Create schedule             |
| GET    | `/schedules`    | Teacher | List own schedules          |
| GET    | `/schedules/me` | Student | List own upcoming schedules |

---

## Enrollment & Payments

### Enrollment

| Method | Endpoint       | Role    | Description          |
| ------ | -------------- | ------- | -------------------- |
| POST   | `/enrollments` | Student | Enroll into a course |

### Payments

| Method | Endpoint                        | Role          | Description                        |
| ------ | ------------------------------- | ------------- | ---------------------------------- |
| POST   | `/enrollments/{enrollment}/pay` | Student       | Upload proof (multipart/form-data) |
| GET    | `/my-payments`                  | Student       | List own payments                  |
| GET    | `/payments`                     | Admin/Finance | List all payments                  |
| PATCH  | `/payments/{payment}/confirm`   | Admin/Finance | Confirm (paid)                     |
| PATCH  | `/payments/{payment}/reject`    | Admin/Finance | Reject payment                     |

---

## Materials

| Method | Endpoint                      | Role            | Description     |
| ------ | ----------------------------- | --------------- | --------------- |
| GET    | `/courses/{course}/materials` | Teacher/Student | List materials  |
| POST   | `/courses/{course}/materials` | Teacher         | Create material |
| PATCH  | `/materials/{material}`       | Teacher         | Update material |
| DELETE | `/materials/{material}`       | Teacher         | Delete material |

---

## Quizzes & Questions (Teacher)

### Quiz CRUD

| Method | Endpoint          | Description          |
| ------ | ----------------- | -------------------- |
| GET    | `/quizzes`        | List my quizzes      |
| POST   | `/quizzes`        | Create quiz          |
| GET    | `/quizzes/{quiz}` | Get quiz + questions |
| PATCH  | `/quizzes/{quiz}` | Update quiz          |
| DELETE | `/quizzes/{quiz}` | Delete quiz          |

### Questions

| Method | Endpoint                        | Description                     |
| ------ | ------------------------------- | ------------------------------- |
| POST   | `/quizzes/{quiz}/questions`     | Add question + choices          |
| PATCH  | `/quizzes/questions/{question}` | Update question & reset choices |
| DELETE | `/quizzes/questions/{question}` | Delete question                 |

---

## Quiz Flow (Student)

| Method | Endpoint                   | Description                       |
| ------ | -------------------------- | --------------------------------- |
| GET    | `/available-quizzes`       | List quizzes available to student |
| GET    | `/quizzes/{quiz}/start`    | Start quiz (get questions)        |
| POST   | `/quizzes/{quiz}/submit`   | Submit answers & auto-grade       |
| GET    | `/quiz-attempts/{attempt}` | View attempt result               |
| GET    | `/certificate/{attempt}`   | Download PDF certificate          |

---

## Essay Grading (Admin/Teacher)

| Method | Endpoint                  | Description                           |
| ------ | ------------------------- | ------------------------------------- |
| GET    | `/essay-answers`          | List ungraded essay answers           |
| PATCH  | `/essay-answers/{answer}` | Grade essay (assign score & feedback) |

---

## Quiz Statistics (Admin/Teacher)

| Method | Endpoint                          | Description                               |
| ------ | --------------------------------- | ----------------------------------------- |
| GET    | `/quizzes/{quiz}/stats`           | Stats for one quiz (avg, high, low, rank) |
| GET    | `/courses/{course}/quizzes/stats` | Stats for all quizzes in a course         |
| GET    | `/quizzes/{quiz}/stats/export`    | Download CSV ranking                      |
| GET    | `/quizzes/{quiz}/questions/stats` | Stats per question (correct/incorrect)    |

---

## Assignments & Submissions

### Assignments (Teacher)

| Method | Endpoint                        | Description       |
| ------ | ------------------------------- | ----------------- |
| GET    | `/courses/{course}/assignments` | List assignments  |
| POST   | `/courses/{course}/assignments` | Create assignment |
| PATCH  | `/assignments/{assignment}`     | Update assignment |
| DELETE | `/assignments/{assignment}`     | Delete assignment |

### Submissions

| Method | Endpoint                                | Role    | Description                      |
| ------ | --------------------------------------- | ------- | -------------------------------- |
| GET    | `/courses/{course}/assignments`         | Student | List assignments                 |
| POST   | `/assignments/{assignment}/submit`      | Student | Upload submission (multipart)    |
| GET    | `/assignments/{assignment}/submissions` | Teacher | List all submissions             |
| PATCH  | `/submissions/{submission}/grade`       | Teacher | Grade & feedback, notify student |

---

## Notifications

| Method | Endpoint                             | Description             |
| ------ | ------------------------------------ | ----------------------- |
| GET    | `/notifications`                     | List user notifications |
| PATCH  | `/notifications/{notification}/read` | Mark as read            |

---

## CMS: Landing Page

### Sections

| Method | Endpoint             | Description |
| ------ | -------------------- | ----------- |
| GET    | `/landing/sections`  | Public list |
| GET    | `/cms/sections`      | Admin list  |
| POST   | `/cms/sections`      | Create      |
| GET    | `/cms/sections/{id}` | Detail      |
| PATCH  | `/cms/sections/{id}` | Update      |
| DELETE | `/cms/sections/{id}` | Delete      |

### Events

| Method | Endpoint           | Description |
| ------ | ------------------ | ----------- |
| GET    | `/landing/events`  | Public list |
| GET    | `/cms/events`      | Admin list  |
| POST   | `/cms/events`      | Create      |
| GET    | `/cms/events/{id}` | Detail      |
| PATCH  | `/cms/events/{id}` | Update      |
| DELETE | `/cms/events/{id}` | Delete      |

### Pricing Plans

| Method | Endpoint            | Description |
| ------ | ------------------- | ----------- |
| GET    | `/landing/pricing`  | Public list |
| GET    | `/cms/pricing`      | Admin list  |
| POST   | `/cms/pricing`      | Create      |
| GET    | `/cms/pricing/{id}` | Detail      |
| PATCH  | `/cms/pricing/{id}` | Update      |
| DELETE | `/cms/pricing/{id}` | Delete      |

### Mentors (Teachers)

| Method | Endpoint           | Description          |
| ------ | ------------------ | -------------------- |
| GET    | `/landing/mentors` | Public list teachers |

---

## Search & Filter

| Method | Endpoint  | Query Params              |
| ------ | --------- | ------------------------- | --------- | ------- | ------ | -------- | ----- |
| GET    | `/search` | `?query={q}&type={courses | materials | quizzes | events | teachers | all}` |

---

## Dashboard

| Method | Endpoint     | Description                                      |
| ------ | ------------ | ------------------------------------------------ |
| GET    | `/dashboard` | Role‑based stats (admin/teacher/student/finance) |

---

> **Note:** All protected endpoints require `Authorization: Bearer {token}` header.
