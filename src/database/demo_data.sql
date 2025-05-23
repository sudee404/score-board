-- Demo data for Score Board application

-- Clear existing data (if any)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE scores;
TRUNCATE TABLE judges;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Insert sample judges
INSERT INTO judges (username, display_name) VALUES 
('alex_judge', 'Alex Rodriguez'),
('sarah_judge', 'Sarah Johnson'),
('mike_judge', 'Michael Williams'),
('emma_judge', 'Emma Davis'),
('james_judge', 'James Miller');

-- Insert sample users/participants
INSERT INTO users (name) VALUES 
('John Doe'),
('Jane Smith'),
('Michael Johnson'),
('Emily Brown'),
('David Wilson'),
('Sophia Martinez'),
('Robert Taylor'),
('Olivia Anderson'),
('William Thomas'),
('Ava Jackson');

-- Insert sample scores
-- Judge 1 (Alex) scores
INSERT INTO scores (judge_id, user_id, points) VALUES 
(1, 1, 85), -- Alex scores John Doe
(1, 2, 92), -- Alex scores Jane Smith
(1, 3, 78), -- Alex scores Michael Johnson
(1, 4, 95), -- Alex scores Emily Brown
(1, 5, 88); -- Alex scores David Wilson

-- Judge 2 (Sarah) scores
INSERT INTO scores (judge_id, user_id, points) VALUES 
(2, 1, 82), -- Sarah scores John Doe
(2, 2, 90), -- Sarah scores Jane Smith
(2, 3, 75), -- Sarah scores Michael Johnson
(2, 6, 88), -- Sarah scores Sophia Martinez
(2, 7, 79); -- Sarah scores Robert Taylor

-- Judge 3 (Michael) scores
INSERT INTO scores (judge_id, user_id, points) VALUES 
(3, 3, 80), -- Michael scores Michael Johnson
(3, 4, 93), -- Michael scores Emily Brown
(3, 5, 85), -- Michael scores David Wilson
(3, 8, 91), -- Michael scores Olivia Anderson
(3, 9, 77); -- Michael scores William Thomas

-- Judge 4 (Emma) scores
INSERT INTO scores (judge_id, user_id, points) VALUES 
(4, 2, 88), -- Emma scores Jane Smith
(4, 4, 90), -- Emma scores Emily Brown
(4, 6, 82), -- Emma scores Sophia Martinez
(4, 8, 94), -- Emma scores Olivia Anderson
(4, 10, 89); -- Emma scores Ava Jackson

-- Judge 5 (James) scores
INSERT INTO scores (judge_id, user_id, points) VALUES 
(5, 1, 79), -- James scores John Doe
(5, 3, 84), -- James scores Michael Johnson
(5, 5, 91), -- James scores David Wilson
(5, 7, 86), -- James scores Robert Taylor
(5, 9, 80); -- James scores William Thomas

-- Add some more diverse scores
INSERT INTO scores (judge_id, user_id, points) VALUES 
(1, 6, 45), -- Alex scores Sophia Martinez (low score)
(2, 8, 98), -- Sarah scores Olivia Anderson (high score)
(3, 10, 30), -- Michael scores Ava Jackson (very low score)
(4, 9, 65), -- Emma scores William Thomas (medium score)
(5, 10, 72); -- James scores Ava Jackson (medium-high score)
