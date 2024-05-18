CREATE TABLE board_manage(
    idx INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) DEFAULT '' COMMENT '게시판 이름',
    `btype` ENUM('board', 'gallery') DEFAULT 'board' COMMENT '게시판타입',
    `cnt` INTEGER DEFAULT 0 COMMENT `게시물 수`,
    `create_at` DATETIME,

    PRIMARY KEY(idx)
);