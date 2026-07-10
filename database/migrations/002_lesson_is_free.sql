-- Migration: free / paid status per lesson.
-- Apply to an existing database that was created before this feature:
--   mysql -u root e_learning < database/migrations/002_lesson_is_free.sql
--
-- (Fresh imports of database/learning.sql already include this column.)

-- `is_free = 1` lessons are previewable by students who have NOT bought the
-- course; `is_free = 0` (default) lessons require ownership to watch.
ALTER TABLE lessons
    ADD COLUMN is_free TINYINT(1) NOT NULL DEFAULT 0 AFTER video;
