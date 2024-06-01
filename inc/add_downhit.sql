ALTER TABLE board ADD COLUMN downhit VARCHAR(20) DEFAULT '' AFTER hit;

UPDATE board SET downhit='4?0' WHERE idx = 40;