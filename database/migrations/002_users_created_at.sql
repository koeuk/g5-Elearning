-- Migration: track when each user account was created, so the admin dashboard
-- can chart new users (sign-ups) over time.
--
-- Apply to an existing database:
--   mysql -u root e_learning < database/migrations/002_users_created_at.sql
-- (Fresh imports of database/learning.sql already include this column.)

ALTER TABLE `users` ADD COLUMN `created_at` DATETIME DEFAULT NULL;

-- Backfill: use each user's first purchase date where we have one (so existing
-- data shows a realistic sign-up trend), otherwise today.
UPDATE `users` u
LEFT JOIN (SELECT user_id, MIN(date) AS first_pay FROM payments GROUP BY user_id) p
  ON p.user_id = u.user_id
SET u.created_at = COALESCE(p.first_pay, CURDATE())
WHERE u.created_at IS NULL;
