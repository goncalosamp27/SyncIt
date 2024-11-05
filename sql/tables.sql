show search_path;
SET search_path TO lbaw24115;

DROP TABLE IF EXISTS member CASCADE;
DROP TABLE IF EXISTS artist CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS event CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS tag CASCADE;
DROP TABLE IF EXISTS event_tag CASCADE;
DROP TABLE IF EXISTS ticket CASCADE;
DROP TABLE IF EXISTS poll CASCADE;
DROP TABLE IF EXISTS option CASCADE;
DROP TABLE IF EXISTS invitation CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS voting CASCADE;
DROP TABLE IF EXISTS poll_notification CASCADE;
DROP TABLE IF EXISTS follow_notification CASCADE;
DROP TABLE IF EXISTS comment_notification CASCADE;
DROP TABLE IF EXISTS invitation_notification CASCADE;
DROP TABLE IF EXISTS following CASCADE;
DROP TABLE IF EXISTS rating CASCADE;
DROP TABLE IF EXISTS restriction CASCADE;
DROP TABLE IF EXISTS restriction_notification CASCADE;

DROP DOMAIN IF EXISTS email_domain CASCADE;
DROP DOMAIN IF EXISTS price_domain CASCADE;
DROP DOMAIN IF EXISTS event_type_domain CASCADE;
DROP DOMAIN IF EXISTS member_status_domain CASCADE;
DROP DOMAIN IF EXISTS refund_policy CASCADE;
DROP DOMAIN IF EXISTS username_domain CASCADE;
DROP DOMAIN IF EXISTS name_domain CASCADE;
DROP DOMAIN IF EXISTS password_domain CASCADE;
DROP DOMAIN IF EXISTS rating_domain CASCADE;

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

CREATE DOMAIN name_domain AS VARCHAR(50)
CHECK (
    CHAR_LENGTH(VALUE) BETWEEN 3 AND 50 
    AND VALUE ~ '^[A-Za-z0-9_ ]+$'
);

CREATE DOMAIN password_domain AS VARCHAR(100)
CHECK (CHAR_LENGTH(VALUE) BETWEEN 8 AND 100);

CREATE DOMAIN rating_domain AS DECIMAL(2, 1)
CHECK (VALUE >= 0.0 AND VALUE <= 5.0);


CREATE TABLE member (
    member_id SERIAL PRIMARY KEY,
    username username_domain UNIQUE NOT NULL,
    display_name name_domain NOT NULL,
    email email_domain UNIQUE NOT NULL,
    password password_domain NOT NULL,
    bio VARCHAR(200),
    profile_pic_url VARCHAR(200),
    member_status member_status_domain NOT NULL
);
CREATE INDEX member_username_idx ON member (username);
CREATE INDEX member_display_name_idx ON member (display_name);


CREATE TABLE artist (
    artist_id INT PRIMARY KEY NOT NULL, 
    rating rating_domain NOT NULL,
    FOREIGN KEY (artist_id) REFERENCES member(member_id)
);


CREATE TABLE admin (
    admin_id SERIAL PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);


CREATE TABLE following (
    artist_id INT NOT NULL,
    member_id INT NOT NULL,

    PRIMARY KEY (artist_id, member_id),

    FOREIGN KEY (artist_id) REFERENCES artist(artist_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES member(member_id) ON DELETE CASCADE,

    CHECK (artist_id <> member_id)
);


CREATE TABLE event (
    event_id SERIAL PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_date TIMESTAMP NOT NULL CHECK (event_date >= CURRENT_DATE + INTERVAL '1 day'),
    location VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    refund refund_policy NOT NULL,
    price price_domain NOT NULL,
    type_of_event event_type_domain NOT NULL,
    rating rating_domain NOT NULL,
    artist_id INT NOT NULL,
    FOREIGN KEY (artist_id) REFERENCES artist(artist_id)
);
CREATE INDEX event_date_idx ON event (event_date);
CREATE INDEX event_name_idx ON event (event_name);
CREATE INDEX event_rating_idx ON event (rating);
CREATE INDEX event_artist_id_idx ON event (artist_id);


CREATE TABLE comment (
    comment_id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    comment_date TIMESTAMP NOT NULL CHECK (comment_date >= CURRENT_DATE),
    event_id INT NOT NULL,
    member_id INT NOT NULL,
    response_comment_id INT,
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (response_comment_id) REFERENCES comment(comment_id)
);
CREATE INDEX comment_event_id_idx ON comment (event_id);
CREATE INDEX comment_member_id_idx ON comment (member_id);
CREATE INDEX comment_date_idx ON comment (comment_date);


CREATE TABLE tag (
    tag_id SERIAL PRIMARY KEY,
    tag_name VARCHAR(20) NOT NULL,
    color VARCHAR(6) NOT NULL
);
CREATE INDEX tag_name_idx ON tag (tag_name);


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
    ticket_date TIMESTAMP NOT NULL CHECK (ticket_date >= CURRENT_DATE),
    member_id INT NOT NULL UNIQUE,
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);


CREATE TABLE poll (
    poll_id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
    start_date DATE NOT NULL CHECK (start_date >= CURRENT_DATE),
    end_date DATE NOT NULL CHECK (end_date > start_date),
    FOREIGN KEY (event_id) REFERENCES event(event_id)
);


CREATE TABLE option (
    option_id SERIAL PRIMARY KEY,
    option_name VARCHAR(100) NOT NULL,
    poll_id INT NOT NULL,

    FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
);


CREATE TABLE invitation (
    invitation_id SERIAL PRIMARY KEY,
    invitation_message TEXT,
    invitation_date TIMESTAMP NOT NULL CHECK (invitation_date >= CURRENT_DATE),
    event_id INT NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id),
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);


CREATE TABLE notification (
    notification_id SERIAL PRIMARY KEY,
    notification_message TEXT NOT NULL,
    notification_date TIMESTAMP NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);
CREATE INDEX notification_date_idx ON notification (notification_date);

CREATE TABLE invitation_notification (
    notification_id INT PRIMARY KEY,
    invitation_id INT NOT NULL,
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (invitation_id) REFERENCES invitation(invitation_id)
);

CREATE TABLE follow_notification(
    notification_id INT PRIMARY KEY,
    follower_id INT NOT NULL,

    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (follower_id) REFERENCES member(member_id)
);  

CREATE TABLE comment_notification (
    notification_id INT PRIMARY KEY,
    comment_id INT NOT NULL,
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (comment_id) REFERENCES comment(comment_id)
);

CREATE TABLE poll_notification (
    notification_id INT PRIMARY KEY,
    poll_id INT NOT NULL,
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id)
);

CREATE TABLE voting (
    voting_id SERIAL PRIMARY KEY,
    poll_id INT NOT NULL,
    option_id INT NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (poll_id) REFERENCES poll(poll_id),
    FOREIGN KEY (option_id) REFERENCES option(option_id),
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    UNIQUE (poll_id, member_id) 
);

CREATE TABLE rating (
	event_id INT NOT NULL,
	member_id INT NOT NULL,
	rating rating_domain NOT NULL,
	PRIMARY KEY (event_id, member_id),
	FOREIGN KEY (event_id) REFERENCES event(event_id),
	FOREIGN KEY (member_id) REFERENCES member(member_id)
);

CREATE TABLE restriction (
    restriction_id SERIAL PRIMARY KEY,
    member_id INT NOT NULL,
    duration INTERVAL NOT NULL,
    admin_id INT NOT NULL,
    start TIMESTAMP NOT NULL,
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
);

CREATE TABLE restriction_notification (
    notification_id INT PRIMARY KEY,
    restriction_id INT NOT NULL,
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id),
    FOREIGN KEY (restriction_id) REFERENCES restriction(restriction_id)
);

-- Upon account deletion, shared user data (e.g. comments, reviews, likes) is kept but made anonymous.
CREATE OR REPLACE FUNCTION anonymize_data() RETURNS TRIGGER AS
$BODY$
BEGIN

    IF TG_TABLE_NAME = 'member' THEN
        UPDATE comment
        SET member_id = 1 -- DEFAULT USER
        WHERE member_id = OLD.member_id;

    ELSIF TG_TABLE_NAME = 'artist' THEN
        UPDATE event
        SET artist_id = 1-- DEFAULT USER
        WHERE artist_id = OLD.artist_id;
    END IF;

    RETURN OLD;
END;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER before_member_delete
    BEFORE DELETE ON member
    FOR EACH ROW
    EXECUTE FUNCTION anonymize_data();

CREATE OR REPLACE TRIGGER before_artist_delete
    BEFORE DELETE ON artist
    FOR EACH ROW
    EXECUTE FUNCTION anonymize_data();

CREATE OR REPLACE FUNCTION create_artist_trigger_function()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM artist WHERE artist_id = NEW.artist_id) THEN
        INSERT INTO artist (artist_id, rating) VALUES (NEW.artist_id, 0);
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER create_artist_after_event_insert
    AFTER INSERT ON event
    FOR EACH ROW
    EXECUTE FUNCTION create_artist_trigger_function();


-- BR04: Suspended or banned accounts cannot interact with the website (i.e. comment, purchase tickets,...)
CREATE OR REPLACE FUNCTION check_member_status() RETURNS TRIGGER AS
$BODY$
BEGIN
    -- Check if the member status is Suspended or Banned
    IF EXISTS (
        SELECT 1 
        FROM member
        WHERE member_id = NEW.member_id 
        AND member_status IN ('Suspended', 'Banned')  -- Use string literals if member_status is text
    ) THEN
        RAISE EXCEPTION 'Suspended or banned accounts cannot interact with the website.';
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER before_comment
    BEFORE INSERT ON comment
    FOR EACH ROW
    EXECUTE FUNCTION check_member_status();

CREATE OR REPLACE TRIGGER before_ticket_purchase
    BEFORE INSERT ON ticket
    FOR EACH ROW
    EXECUTE FUNCTION check_member_status();

CREATE OR REPLACE TRIGGER before_invitation
    BEFORE INSERT ON invitation
    FOR EACH ROW
    EXECUTE FUNCTION check_member_status();

CREATE OR REPLACE TRIGGER before_following
    BEFORE INSERT ON following
    FOR EACH ROW
    EXECUTE FUNCTION check_member_status();

CREATE OR REPLACE TRIGGER before_rating
    BEFORE INSERT ON following
    FOR EACH ROW
    EXECUTE FUNCTION check_member_status();

CREATE OR REPLACE TRIGGER before_voting
    BEFORE INSERT ON following
    FOR EACH ROW
    EXECUTE FUNCTION check_member_status();

CREATE OR REPLACE FUNCTION update_artist_rating() 
RETURNS TRIGGER AS $$
BEGIN
    UPDATE artist 
    SET rating = COALESCE((
        SELECT AVG(r.rating) 
        FROM rating r
        JOIN event e ON r.event_id = e.event_id
        WHERE e.artist_id = (
            SELECT artist_id
            FROM event
            WHERE event_id = NEW.event_id
        )
    ), 0)  -- Use 0 if the average is NULL
    WHERE artist_id = (
        SELECT artist_id
        FROM event
        WHERE event_id = NEW.event_id
    );

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE TRIGGER after_rating_insert
    AFTER INSERT ON rating
    FOR EACH ROW
    EXECUTE FUNCTION update_artist_rating();


CREATE OR REPLACE FUNCTION comment_handler()
RETURNS TRIGGER AS $$
DECLARE
    new_notification_id INT; -- Declare the variable to hold the new notification ID
BEGIN
    -- Insert the notification and get the new notification_id in one step
    INSERT INTO notification (notification_message, notification_date, member_id)
    VALUES (
        'New comment added: ' || NEW.text,
        CURRENT_TIMESTAMP,
        NEW.member_id
    )
    RETURNING notification_id INTO new_notification_id;  -- Capture the new notification ID

    -- Insert the notification record into comment_notification
    INSERT INTO comment_notification (notification_id, comment_id)
    VALUES (new_notification_id, NEW.comment_id);

    RETURN NEW;  -- Return the new comment row
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION follow_handler()
RETURNS TRIGGER AS $$
DECLARE
    new_notification_id INT;  -- Variable to hold the new notification ID
BEGIN
    -- Insert the notification and get the new notification_id in one step
    INSERT INTO notification (notification_message, notification_date, member_id)
    VALUES (
        'You have a new follower!',
        CURRENT_TIMESTAMP,
        NEW.artist_id  -- The artist being followed receives the notification
    )
    RETURNING notification_id INTO new_notification_id;  -- Capture the new notification ID

    -- Insert the follow notification record linking the notification and the follower
    INSERT INTO follow_notification (notification_id, follower_id)
    VALUES (new_notification_id, NEW.member_id);  -- The follower is the member who initiated the follow

    RETURN NEW;  -- Return the new following row
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER after_follow_insert
AFTER INSERT ON following
FOR EACH ROW
EXECUTE FUNCTION follow_handler();


CREATE OR REPLACE FUNCTION invitation_handler()
RETURNS TRIGGER AS $$
DECLARE
    new_notification_id INT;  -- Variable to hold the new notification ID
BEGIN
    -- Insert the notification and get the new notification_id in one step
    INSERT INTO notification (notification_message, notification_date, member_id)
    VALUES (
        'You have a new invitation: ' || NEW.invitation_message,
        CURRENT_TIMESTAMP,
        NEW.member_id
    )
    RETURNING notification_id INTO new_notification_id;  -- Capture the new notification ID

    -- Insert the notification record into invitation_notification
    INSERT INTO invitation_notification (notification_id, invitation_id)
    VALUES (new_notification_id, NEW.invitation_id);  -- Link notification with the invitation

    RETURN NEW;  -- Return the new invitation row
END;
$$ LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION poll_end_handler()
RETURNS VOID AS $$
DECLARE
    poll_record RECORD;
BEGIN
    FOR poll_record IN
        SELECT p.poll_id, p.event_id, p.member_id  -- Assuming you want to notify the creator
        FROM poll p
        WHERE p.end_date <= CURRENT_TIMESTAMP
          AND NOT EXISTS (
              SELECT 1 
              FROM notification n
              WHERE n.notification_message = 'Poll has ended for event ID: ' || p.event_id
              AND n.member_id = p.member_id
              AND n.notification_date >= CURRENT_TIMESTAMP - INTERVAL '1 hour'  -- Prevents multiple notifications
          )
    LOOP
        PERFORM generate_notification(
            'poll',
            poll_record.member_id,  -- The member who created the poll
            poll_record.poll_id,
            'Poll has ended for event ID: ' || poll_record.event_id
        );
    END LOOP;
END;
$$ LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION restriction_start_handler()
RETURNS TRIGGER AS $$
DECLARE
    new_notification_id INT;  -- Variable to hold the new notification ID
BEGIN
    -- Insert the notification and get the new notification_id in one step
    INSERT INTO notification (notification_message, notification_date, member_id)
    VALUES (
        'A new restriction has been applied to you.',
        CURRENT_TIMESTAMP,
        NEW.member_id
    )
    RETURNING notification_id INTO new_notification_id;  -- Capture the new notification ID

    -- Insert the notification record into restriction_notification
    INSERT INTO restriction_notification (notification_id, restriction_id)
    VALUES (new_notification_id, NEW.restriction_id);  -- Link notification with the restriction

    -- Update member status based on restriction duration
    IF NEW.duration = '0 days' THEN
        UPDATE member
        SET member_status = 'Banned'  -- Set status to Banned if duration is 0 days
        WHERE member_id = NEW.member_id;
    ELSE
        UPDATE member
        SET member_status = 'Suspended'  -- Set status to Suspended otherwise
        WHERE member_id = NEW.member_id;
    END IF;

    RETURN NEW;  -- Return the new restriction row
END;
$$ LANGUAGE plpgsql;




-- Trigger for comment table
CREATE TRIGGER after_comment_insert
AFTER INSERT ON comment
FOR EACH ROW
EXECUTE FUNCTION comment_handler();

-- Trigger for invitation table
CREATE TRIGGER after_invitation_insert
AFTER INSERT ON invitation
FOR EACH ROW
EXECUTE FUNCTION invitation_handler();


-- Trigger for restriction table
CREATE TRIGGER after_restriction_insert
AFTER INSERT ON restriction
FOR EACH ROW
EXECUTE FUNCTION restriction_start_handler();


-- FULL TEXT SEARCH -> members
ALTER TABLE member ADD COLUMN fts_username tsvector;
ALTER TABLE member ADD COLUMN fts_display_name tsvector;

CREATE OR REPLACE FUNCTION member_fts_trigger() RETURNS trigger AS $$
BEGIN
    NEW.fts_username := to_tsvector('english', COALESCE(NEW.username, ''));
    NEW.fts_display_name := to_tsvector('english', COALESCE(NEW.display_name, ''));
    RETURN NEW;
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER member_fts_update BEFORE INSERT OR UPDATE ON member
FOR EACH ROW EXECUTE FUNCTION member_fts_trigger();

CREATE INDEX member_fts_username_idx ON member USING GIN (fts_username);
CREATE INDEX member_fts_display_name_idx ON member USING GIN (fts_display_name);

-- FULL TEXT SEARCH -> events
ALTER TABLE event ADD COLUMN fts_name tsvector;
ALTER TABLE event ADD COLUMN fts_location tsvector;

CREATE OR REPLACE FUNCTION event_fts_trigger() RETURNS trigger AS $$
BEGIN
    NEW.fts_name := to_tsvector('english', COALESCE(NEW.event_name, ''));
    NEW.fts_location := to_tsvector('english', COALESCE(NEW.location, ''));
    RETURN NEW;
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER event_fts_update BEFORE INSERT OR UPDATE ON event
FOR EACH ROW EXECUTE FUNCTION event_fts_trigger();

CREATE INDEX event_fts_name_idx ON event USING GIN (fts_name);
CREATE INDEX event_fts_location_idx ON event USING GIN (fts_location);


