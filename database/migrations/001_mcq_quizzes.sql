-- Migration: multiple-choice quizzes with in-app submission + scoring.
-- Apply to an existing database that was created before this feature:
--   mysql -u root e_learning < database/migrations/001_mcq_quizzes.sql
--
-- (Fresh imports of database/learning.sql already include these columns.)

-- Each `quizzes` row holds one MCQ question as JSON in `content`:
--   {"q":"...", "options":["A","B","C","D"], "answer":1}
ALTER TABLE `quizzes` MODIFY `content` TEXT DEFAULT NULL;

-- `quiz_sumit` records a student's auto-graded result (image kept for the
-- legacy screenshot flow, now nullable).
ALTER TABLE `quiz_sumit` MODIFY `image` VARCHAR(255) NULL;
ALTER TABLE `quiz_sumit` ADD COLUMN `score` INT NULL;
ALTER TABLE `quiz_sumit` ADD COLUMN `total` INT NULL;
ALTER TABLE `quiz_sumit` ADD COLUMN `created_at` DATETIME NULL;
