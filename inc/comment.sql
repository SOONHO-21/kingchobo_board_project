# 댓글 
CREATE TABLE `comment`(
    idx INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    pidx INTEGER NOT NULL,
    id VARCHAR(50) DEFAULT '' COMMENT '글작성자',
    content TEXT COMMENT '댓글내용',
    create_at DATETIME NOT NULL,
    ip VARCHAR(30),
    PRIMARY KEY(idx)
);

ALTER TABLE board ADD column comment_cnt INTEGER unsigned DEFAULT 0 AFTER hit;