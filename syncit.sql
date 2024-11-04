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

CREATE DOMAIN email_domain AS VARCHAR(255)
CHECK (POSITION('@' IN VALUE) > 1);

CREATE DOMAIN price_domain AS DECIMAL(6,2)
CHECK (VALUE >= 0);

CREATE DOMAIN event_type_domain AS VARCHAR(10)
CHECK (VALUE IN ('Public', 'Private'));

CREATE DOMAIN member_status_domain AS VARCHAR(10)
CHECK (VALUE IN ('Active', 'Suspended', 'Banned'));

CREATE DOMAIN refund_policy AS DECIMAL(5, 2)
CHECK (VALUE BETWEEN 0 AND 100);

CREATE DOMAIN username_domain AS VARCHAR(50)
CHECK (
    CHAR_LENGTH(VALUE) BETWEEN 3 AND 50 
    AND VALUE ~ '^[A-Za-z0-9_]+$'
);


CREATE DOMAIN password_domain AS VARCHAR(100)
CHECK (CHAR_LENGTH(VALUE) BETWEEN 8 AND 100);


CREATE TABLE member (
    member_id SERIAL PRIMARY KEY,
    username username_domain UNIQUE NOT NULL,
    email email_domain UNIQUE NOT NULL,
    password password_domain NOT NULL,
    bio VARCHAR(200),
    profile_pic_url VARCHAR(200),
    member_status member_status_domain NOT NULL,
);


CREATE TABLE artist (
    artist_id INT PRIMARY KEY NOT NULL, 
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 0 AND rating <= 5),
    FOREIGN KEY (artist_id) REFERENCES member(member_id)
);
CREATE INDEX artist_id_idx ON event (artist_id);


CREATE TABLE admin (
    admin_id SERIAL PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);


CREATE TABLE event (
    event_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    date TIMESTAMP NOT NULL CHECK (date >= CURRENT_DATE + INTERVAL '1 day'),
    location VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    refund refund_policy NOT NULL,
    price price_domain NOT NULL,
    type_of_event event_type_domain NOT NULL
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 0 AND rating <= 5), 
    artist_id INT NOT NULL,
    FOREIGN KEY (artist_id) REFERENCES member(artist_id)
);
CREATE INDEX event_date_idx ON event (date);
CREATE INDEX event_rating_idx ON event (rating);
CREATE INDEX event_member_id_idx ON event (artist_id);


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
    tag_name VARCHAR(20) NOT NULL,
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

CREATE TABLE ticket (
    ticket_id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
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
    option_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    votes INT NOT NULL,
    poll_id INT NOT NULL,

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

CREATE TABLE invitation_notification (
    notification_id INT NOT NULL,
    invitation_id INT NOT NULL,

    PRIMARY KEY (notification_id, invitation_id),
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (invitation_id) REFERENCES invitation(invitation_id)
);

CREATE TABLE follow_notification(
    notification_id INT NOT NULL,
    follower_id INT NOT NULL,

    PRIMARY KEY (notification_id, follower_id),
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (follower_id) REFERENCES member(member_id)
);  

CREATE TABLE comment_notification (
    notification_id INT NOT NULL,
    comment_id INT NOT NULL,

    PRIMARY KEY (notification_id, comment_id),
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (comment_id) REFERENCES comment(comment_id)
);

CREATE TABLE poll_notification (
    notification_id INT NOT NULL,
    poll_id INT NOT NULL,

    PRIMARY KEY (notification_id, poll_id),
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
);

CREATE TABLE votings (
    voting_id SERIAL PRIMARY KEY,
    poll_id INT NOT NULL,
    option_id INT NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id),
    FOREIGN KEY (option_id) REFERENCES option(option_id),
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    UNIQUE (poll_id, member_id) 
);