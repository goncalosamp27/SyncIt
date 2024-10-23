show search_path;
SET search_path TO lbaw24115;

DROP TABLE IF EXISTS table_template;

CREATE TABLE table_template (
	id INT PRIMARY KEY,
	name VARCHAR(64)
);

INSERT INTO table_template (id, name)
VALUES 
(1, 'Alice'),
(2, 'Ricardo'),
(3, 'José');

CREATE TABLE member (
	member_id SERIAL PRIMARY KEY,
	username VARCHAR(100) UNIQUE NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	password VARCHAR(100) NOT NULL,
	bio VARCHAR(200),
	profile_pic_url VARCHAR(200),
	status INT NOT NULL CHECK (status >= 0 AND status <= 3) -- 0 -> active, 1 -> verified, 2 -> temp ban, 3 -> perma ban
);

CREATE TABLE artist (
    artist_id INT PRIMARY KEY NOT NULL, 
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 0 AND rating <= 5),

    FOREIGN KEY (artist_id) REFERENCES member(member_id)
);

CREATE TABLE admin (
	admin_id SERIAL PRIMARY KEY,
	email VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(100) NOT NULL
);

CREATE TABLE event (
	event_id SERIAL PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE),
	location VARCHAR(100) NOT NULL,
	description TEXT NOT NULL,
	type INT NOT NULL CHECK (type IN (0,1)), -- 0 -> public, 1 -> private
	rating DECIMAL(2,1) NOT NULL CHECK (rating >= 0 AND rating <= 5), 
	member_id INT NOT NULL,

	FOREIGN KEY (member_id) REFERENCES member(member_id)
);

CREATE TABLE comment (
	comment_id SERIAL NOT NULL,
	text TEXT NOT NULL,
	date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE),
	event_id INT NOT NULL,
	member_id INT NOT NULL,
	response_comment_id INT,

	PRIMARY KEY (comment_id, event_id, member_id), 
	FOREIGN KEY (member_id) REFERENCES member(member_id),
	FOREIGN KEY (event_id) REFERENCES event(event_id),
	FOREIGN KEY (response_comment_id) REFERENCES comment(comment_id)
);

CREATE TABLE tag (
	tag_id SERIAL PRIMARY KEY,
	name VARCHAR(20) NOT NULL,
	color VARCHAR(6) NOT NULL
);

CREATE TABLE event_tag ( 
	event_id INT NOT NULL,
	tag_id INT NOT NULL,

	PRIMARY KEY (event_id, tag_id), 
	FOREIGN KEY (event_id) REFERENCES event(event_id),
	FOREIGN KEY (tag_id) REFERENCES tag(tag_id)
);

CREATE TABLE ticket (
	ticket_id SERIAL NOT NULL,
	event_id INT NOT NULL,
	price INT NOT NULL,
	date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE),

	PRIMARY KEY (ticket_id, event_id), 
	FOREIGN KEY (event_id) REFERENCES event(event_id)
);

CREATE TABLE poll (
	poll_id SERIAL NOT NULL,
	event_id INT NOT NULL,
	start_date DATE NOT NULL CHECK (start_date >= CURRENT_DATE),
	end_date DATE NOT NULL CHECK (end_date > start_date),

	PRIMARY KEY (poll_id, event_id), 
	FOREIGN KEY (event_id) REFERENCES event(event_id)
);

CREATE TABLE option (
	option_id SERIAL NOT NULL,
	name VARCHAR(100) NOT NULL,
	votes INT NOT NULL,
	poll_id INT NOT NULL,

	PRIMARY KEY (option_id, poll_id), 
	FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
);

CREATE TABLE invitation (
	invite_id SERIAL NOT NULL,
	invite_message TEXT,
	date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE),
	event_id INT NOT NULL,
	member_id INT NOT NULL,

	PRIMARY KEY (invite_id, event_id, member_id),
	FOREIGN KEY (event_id) REFERENCES event(event_id),
	FOREIGN KEY (member_id) REFERENCES member(member_id)
);

CREATE TABLE notification (
	notification_id SERIAL NOT NULL,
	notification_message TEXT NOT NULL,
	date TIMESTAMP NOT NULL,
	member_id INT NOT NULL,

	PRIMARY KEY (notification_id, member_id), 
	FOREIGN KEY (member_id) REFERENCES member(member_id)
);

SELECT * FROM table_template
