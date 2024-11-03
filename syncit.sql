show search_path;
SET search_path TO lbaw24115;

DROP TABLE IF EXISTS member;
DROP TABLE IF EXISTS artist;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS event;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS event_tag;
DROP TABLE IF EXISTS ticket;
DROP TABLE IF EXISTS poll;
DROP TABLE IF EXISTS option;
DROP TABLE IF EXISTS invitation;
DROP TABLE IF EXISTS notification;


CREATE TABLE member (
    member_id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    bio VARCHAR(200),
    profile_pic_url VARCHAR(200),
    status INT NOT NULL CHECK (status >= 0 AND status <= 3)
);


CREATE TABLE artist (
    artist_id INT PRIMARY KEY NOT NULL, 
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 0 AND rating <= 5),
    FOREIGN KEY (artist_id) REFERENCES member(member_id)
);
CREATE INDEX artist_rating_idx ON artist (rating);


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
    type INT NOT NULL CHECK (type IN (0,1)),
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 0 AND rating <= 5), 
    member_id INT NOT NULL,
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);
CREATE INDEX event_date_idx ON event (date);
CREATE INDEX event_type_idx ON event (type);
CREATE INDEX event_rating_idx ON event (rating);
CREATE INDEX event_member_id_idx ON event (member_id);


CREATE TABLE comment (
    comment_id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE),
    event_id INT NOT NULL,
    member_id INT NOT NULL,
    response_comment_id INT,
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (response_comment_id) REFERENCES comment(comment_id)
);
CREATE INDEX comment_event_id_idx ON comment (event_id);
CREATE INDEX comment_member_id_idx ON comment (member_id);
CREATE INDEX comment_date_idx ON comment (date);


CREATE TABLE tag (
    tag_id SERIAL PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    color VARCHAR(6) NOT NULL
);
CREATE INDEX tag_name_idx ON tag (name);


CREATE TABLE event_tag ( 
    event_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (event_id, tag_id), 
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (tag_id) REFERENCES tag(tag_id)
);
CREATE INDEX event_tag_event_id_idx ON event_tag (event_id);
CREATE INDEX event_tag_tag_id_idx ON event_tag (tag_id);


CREATE TABLE ticket (
    ticket_id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
    price INT NOT NULL,
    date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE),
    owner_id INT NOT NULL UNIQUE,
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (owner_id) REFERENCES member(member_id)
);
CREATE INDEX ticket_event_id_idx ON ticket (event_id);
CREATE INDEX ticket_owner_id_idx ON ticket (owner_id);
CREATE INDEX ticket_date_idx ON ticket (date);


CREATE TABLE poll (
    poll_id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
    start_date DATE NOT NULL CHECK (start_date >= CURRENT_DATE),
    end_date DATE NOT NULL CHECK (end_date > start_date),
    FOREIGN KEY (event_id) REFERENCES event(event_id)
);
CREATE INDEX poll_event_id_idx ON poll (event_id);
CREATE INDEX poll_start_date_idx ON poll (start_date);
CREATE INDEX poll_end_date_idx ON poll (end_date);


CREATE TABLE option (
    option_id SERIAL NOT NULL,
    name VARCHAR(100) NOT NULL,
    votes INT NOT NULL,
    poll_id INT NOT NULL,
    PRIMARY KEY (option_id, poll_id), 
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
);
CREATE INDEX option_votes_idx ON option (votes);
CREATE INDEX option_poll_id_idx ON option (poll_id);


CREATE TABLE invitation (
    invite_id SERIAL PRIMARY KEY,
    invite_message TEXT,
    date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE),
    event_id INT NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);
CREATE INDEX invitation_event_id_idx ON invitation (event_id);
CREATE INDEX invitation_member_id_idx ON invitation (member_id);
CREATE INDEX invitation_date_idx ON invitation (date);


CREATE TABLE notification (
    notification_id SERIAL PRIMARY KEY,
    notification_message TEXT NOT NULL,
    date TIMESTAMP NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);
CREATE INDEX notification_member_id_idx ON notification (member_id);
CREATE INDEX notification_date_idx ON notification (date);
