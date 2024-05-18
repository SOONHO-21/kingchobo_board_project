CREATE TABLE board(
    `idx` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `bcode` char(6) DEFAULT '' COMMENT '게시판 코드',
    `id` VARCHAR(50) DEFAULT '' COMMENT '아이디',
    `name` VARCHAR(50) DEFAULT '' COMMENT '이름',
    `subject` VARCHAR(255) DEFAULT '' COMMENT '제목',
    `content` MEDIUMTEXT COMMENT '내용',
    `hit` INTEGER UNSIGNED DEFAULT 0 COMMENT '조회수',
    `ip` VARCHAR(30) DEFAULT '' COMMENT '글쓴이 IP',
    `create_at` DATETIME NOT NULL COMMENT '글 등록일시',
    INDEX `bcode` (`bcode`),
    INDEX `id`(`id`),
    PRIMARY KEY(idx)
);