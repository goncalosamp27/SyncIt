--show search_path;
--ALTER ROLE postgres 
set search_path to Syncit;

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
DROP TABLE IF EXISTS event_image CASCADE;
DROP TABLE IF EXISTS join_request CASCADE;
DROP TABLE IF EXISTS vote_comment CASCADE;

DROP DOMAIN IF EXISTS email_domain CASCADE;
DROP DOMAIN IF EXISTS price_domain CASCADE;
DROP DOMAIN IF EXISTS event_type_domain CASCADE;
DROP DOMAIN IF EXISTS member_status_domain CASCADE;
DROP DOMAIN IF EXISTS refund_policy CASCADE;
DROP DOMAIN IF EXISTS username_domain CASCADE;
DROP DOMAIN IF EXISTS name_domain CASCADE;
DROP DOMAIN IF EXISTS password_domain CASCADE;
DROP DOMAIN IF EXISTS rating_domain CASCADE;
DROP DOMAIN IF EXISTS request_status_domain CASCADE;

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

CREATE DOMAIN request_status_domain AS VARCHAR(10)
CHECK (VALUE IN ('Approved', 'Rejected', 'Pending'));

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
    event_date TIMESTAMP NOT NULL,
    location VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    refund refund_policy NOT NULL,
    price price_domain NOT NULL,
    type_of_event event_type_domain NOT NULL,
    rating rating_domain NOT NULL,
    artist_id INT NOT NULL,
    capacity INT NOT NULL,
    event_media VARCHAR(100) NOT NULL,
    FOREIGN KEY (artist_id) REFERENCES artist(artist_id)
);

CREATE INDEX event_date_idx ON event (event_date);
CREATE INDEX event_name_idx ON event (event_name);
CREATE INDEX event_rating_idx ON event (rating);
CREATE INDEX event_artist_id_idx ON event (artist_id);

CREATE TABLE join_request (
    request_id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
    member_id INT NOT NULL,
    request_date TIMESTAMP CHECK (request_date >= CURRENT_DATE),
    status request_status_domain NOT NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES member(member_id) ON DELETE CASCADE
);

CREATE TABLE comment (
    comment_id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    comment_date TIMESTAMP NOT NULL CHECK (comment_date >= CURRENT_DATE),
    event_id INT NOT NULL,
    member_id INT NOT NULL,
    response_comment_id INT,
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (response_comment_id) REFERENCES comment(comment_id)
);
CREATE INDEX comment_event_id_idx ON comment (event_id);
CREATE INDEX comment_member_id_idx ON comment (member_id);
CREATE INDEX comment_date_idx ON comment (comment_date);

CREATE TABLE vote_comment (
    vote_comment_id SERIAL PRIMARY KEY,
    comment_id INT NOT NULL,
    member_id INT NOT NULL,
    vote BOOLEAN NOT NULL, -- true = upvote, false = downvote
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    FOREIGN KEY (comment_id) REFERENCES comment(comment_id) ON DELETE CASCADE
);

CREATE TABLE tag (
    tag_id SERIAL PRIMARY KEY,
    tag_type VARCHAR(20) NOT NULL,
    tag_name VARCHAR(20) NOT NULL,
    color VARCHAR(6) NOT NULL
);

CREATE INDEX tag_name_idx ON tag (tag_name);

CREATE TABLE event_tag ( 
    event_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (event_id, tag_id), 
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(tag_id)
);

CREATE TABLE ticket (
    ticket_id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
    ticket_date TIMESTAMP NOT NULL CHECK (ticket_date >= CURRENT_DATE),
    member_id INT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);


CREATE TABLE poll (
    poll_id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
    start_date DATE NOT NULL CHECK (start_date >= CURRENT_DATE),
    end_date DATE NOT NULL CHECK (end_date > start_date),
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE
);


CREATE TABLE option (
    option_id SERIAL PRIMARY KEY,
    option_name VARCHAR(100) NOT NULL,
    poll_id INT NOT NULL,

    FOREIGN KEY (poll_id) REFERENCES poll(poll_id) ON DELETE CASCADE
);


CREATE TABLE invitation (
    invitation_id SERIAL PRIMARY KEY,
    invitation_message TEXT,
    invitation_date TIMESTAMP NOT NULL CHECK (invitation_date >= CURRENT_DATE),
    event_id INT NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES member(member_id)
);

CREATE TABLE notification (
    notification_id SERIAL PRIMARY KEY,
    notification_message TEXT NOT NULL,
    notification_date TIMESTAMP NOT NULL,
    member_id INT NOT NULL,
    FOREIGN KEY (member_id) REFERENCES member(member_id) ON DELETE CASCADE
);
CREATE INDEX notification_date_idx ON notification (notification_date);

CREATE TABLE invitation_notification (
    notification_id INT PRIMARY KEY,
    invitation_id INT NOT NULL,
    FOREIGN KEY (notification_id) REFERENCES notification(notification_id) ON DELETE CASCADE,
    FOREIGN KEY (invitation_id) REFERENCES invitation(invitation_id) ON DELETE CASCADE
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




INSERT INTO member (username, display_name, email, password, bio, profile_pic_url, member_status)
VALUES 
    ('edgar', 'LBAW Teacher', 'lbaw@example.com', '$2y$10$7ElnVwCiQCKHFcNLOShAs.FAFykX1cMLBx8xRI.RJirEWngGSWfmq', 'lbawlbawlbawlbaw', 'default_user.png', 'Active'),
    ('salsadancer', 'Salsa Dance Lover', 'salsadancer@example.com', 'salsadancer123', 'Salsa moves are life!', 'default_user.png', 'Active'),
    ('techbeat', 'Tech Beat DJ', 'techbeat@example.com', 'technosecure99', 'Living for the beats', 'default_user.png', 'Active'),
    ('folksinger', 'Folk Music Singer', 'folksinger@example.com', 'folkpass1234', 'Folk music and stories', 'default_user.png', 'Active'),
    ('classycat', 'Classy Cat Lover', 'classycat@example.com', 'classicalmusic', 'Classical music enthusiast', 'default_user.png', 'Active'),
    ('punkrocker', 'Punk Rock Musician', 'punkrocker@example.com', 'punkrockerpass', 'Punk rock till I die', 'default_user.png','Active'),
    ('djnight', 'DJ Night Vibes', 'djnight@example.com', 'djnight1234', 'Nightlife DJ and mixer', 'default_user.png', 'Active'),
    ('reggae_vibes', 'Reggae Music Vibes', 'reggaevibes@example.com', 'reggaepass2023', 'Feeling those reggae vibes', 'default_user.png', 'Active'),
    ('swingstar', 'Swing Dance Star', 'swingstar@example.com', 'swingpass2023', 'Swing dance for life', 'default_user.png', 'Active'),
    ('drumfiend', 'Drum Music Fiend', 'drumfiend@example.com', 'drumfiend2023', 'Percussion and rhythm addict', 'default_user.png', 'Active'),
    ('violin_virtuoso', 'Violin Virtuoso Master', 'violin@example.com', 'violinpass2023', 'Classical violinist', 'default_user.png', 'Active'),
    ('bassplayer', 'Bass Guitar Player', 'bassplayer@example.com', 'basssecure2023', 'Grooving on the bass', 'default_user.png', 'Active'),
    ('hiphop_dancer', 'Hip Hop Dance Artist', 'hiphopdancer@example.com', 'hiphoppass2023', 'Dance battles and moves', 'default_user.png', 'Active'),
    ('metalhead', 'Heavy Metal Head', 'metalhead@example.com', 'metalhead1234', 'Heavy metal all the way', 'default_user.png', 'Active'),
    ('jazzcat', 'Jazz Music Cat', 'jazzcat@example.com', 'jazzsecure2023', 'Smooth jazz, all day', 'default_user.png', 'Active'),
    ('popqueen', 'Pop Music Queen', 'popqueen@example.com', 'popdivapass2023', 'Pop music enthusiast', 'default_user.png', 'Active'),
    ('raveking', 'Rave Music King', 'raveking@example.com', 'raveking2023', 'Lover of electronic music', 'default_user.png', 'Active'),
    ('bluesfan', 'Blues Music Fan', 'bluesfan@example.com', 'bluesfan2023', 'Appreciating the blues', 'default_user.png', 'Active'),
    ('latinlover', 'Latin Dance Lover', 'latinlover@example.com', 'latinloverpass', 'Latin music and dance', 'default_user.png', 'Active'),
    ('countrygal', 'Country Music Gal', 'countrygal@example.com', 'countrygal2023', 'Country music is my soul', 'default_user.png', 'Active'),
    ('funkfiend', 'Funk Music Fiend', 'funkfiend@example.com', 'funkfiend2023', 'Funk and groove lover', 'default_user.png', 'Active'),
    ('jazzman', 'Jazz Music Man', 'jazzman@example.com', 'jazzman2023', 'Living for jazz sounds', 'default_user.png', 'Active'),
    ('rocknroller', 'Rock and Roll Player', 'rocknroller@example.com', 'rocknroller2023', 'Rock and roll fanatic', 'default_user.png', 'Active'),
    ('dancerpro', 'Professional Dancer', 'dancerpro@example.com', 'dancepro2023', 'Professional dancer', 'default_user.png', 'Active'),
    ('synthwave', 'Synth Wave Music', 'synthwave@example.com', 'synthwave2023', 'Synthwave music lover', 'default_user.png', 'Active'),
    ('guitarhero', 'Guitar Hero Player', 'guitarhero@example.com', 'guitarhero2023', 'Guitar is life!', 'default_user.png', 'Active'),
    ('soulbrother', 'Soul Brother Music', 'soulbrother@example.com', 'soulbrother2023', 'Smooth soul sounds', 'default_user.png', 'Active'),
    ('classicrocker', 'Classic Rock Musician', 'classicrocker@example.com', 'classicrocker2023', 'Classic rock fan', 'default_user.png', 'Active'),
    ('rnbvibes', 'RnB Music Vibes', 'rnbvibes@example.com', 'rnbvibes2023', 'RnB enthusiast', 'default_user.png', 'Active'),
    ('saxplayer', 'Saxophone Player', 'saxplayer@example.com', 'saxplayer2023', 'Jazz and blues saxophonist', 'default_user.png', 'Active'),
    ('bollywoodstar', 'Bollywood Dance Star', 'bollywoodstar@example.com', 'bollywoodstar2023', 'Bollywood dance lover', 'default_user.png', 'Active'),
    ('flamenco_queen', 'Flamenco Dance Queen', 'flamencoqueen@example.com', 'flamencoqueen2023', 'Passionate about Flamenco', 'default_user.png', 'Active'),
    ('edmlover', 'EDM Music Lover', 'edmlover@example.com', 'edmlover2023', 'EDM events enthusiast', 'default_user.png', 'Active'),
    ('thecomposer', 'The Music Composer', 'composer@example.com', 'thecomposer2023', 'Classical and orchestral music', 'default_user.png', 'Active'),
    ('urbanvibes', 'Urban Dance Vibes', 'urbanvibes@example.com', 'urbanvibes2023', 'Love for urban dance styles', 'default_user.png', 'Active'),
    ('tapdancepro', 'Tap Dance Professional', 'tapdancepro@example.com', 'tapdancepro2023', 'Tap dance artist', 'default_user.png', 'Active'),
    ('mambo_king', 'Mambo Dance King', 'mamboking@example.com', 'mambo_king2023', 'Mambo and Latin music fan', 'default_user.png', 'Active'),
    ('brassblues', 'Brass Blues Music', 'brassblues@example.com', 'brassblues2023', 'Brass instrument lover', 'default_user.png', 'Active'),
    ('loungecat', 'Lounge Music Cat', 'loungecat@example.com', 'loungecat2023', 'Lounge and chill music', 'default_user.png', 'Active'),
    ('folkdancer', 'Folk Dance Artist', 'folkdancer@example.com', 'folkdancer2023', 'Traditional folk dance', 'default_user.png', 'Active'),
    ('countryboy', 'Country Music Boy', 'countryboy@example.com', 'countryboy2023', 'Country music fan', 'default_user.png', 'Active'),
    ('balletlover', 'Ballet Dance Lover', 'balletlover@example.com', 'balletlover2023', 'Ballet enthusiast', 'default_user.png', 'Active'),
    ('drumbeats', 'Drum Beats Artist', 'drumbeats@example.com', 'drumbeats2023', 'Drumming and rhythm', 'default_user.png', 'Active'),
    ('gospelgal', 'Gospel Music Gal', 'gospelgal@example.com', 'gospelgal2023', 'Gospel music lover', 'default_user.png', 'Active'),
    ('discofever', 'Disco Music Fever', 'discofever@example.com', 'discofever2023', 'Disco music is life', 'default_user.png', 'Active'),
    ('pianistpro', 'Professional Pianist', 'pianistpro@example.com', 'pianistpro2023', 'Professional pianist', 'default_user.png', 'Active'),
    ('soulqueen', 'Soul Music Queen', 'soulqueen@example.com', 'soulqueen2023', 'Soul music lover', 'default_user.png', 'Active'),
    ('salsaqueen', 'Salsa Dance Queen', 'salsaqueen@example.com', 'salsaqueen2023', 'Queen of Salsa', 'default_user.png', 'Active'),
    ('rockgod', 'Rock God Musician', 'rockgod@example.com', 'rockgod2023', 'Rock music is in my veins', 'default_user.png', 'Active'),
    ('reggaemaster', 'Reggae Music Master', 'reggaemaster@example.com', 'reggaemaster2023', 'Roots and reggae', 'default_user.png', 'Active'),
    ('danceboss', 'Dance Choreography Boss', 'danceboss@example.com', 'danceboss2023', 'Professional choreographer', 'default_user.png', 'Active'),
    ('jazzsoul', 'Jazz and Soul Music', 'jazzsoul@example.com', 'jazzsoul2023', 'Smooth jazz and soul', 'default_user.png', 'Active'),
    ('trumpetstar', 'Trumpet Music Star', 'trumpetstar@example.com', 'trumpetstar2023', 'Trumpet player', 'default_user.png', 'Active'),
    ('alternativefan', 'Alternative Music Fan', 'alternativefan@example.com', 'alternativefan2023', 'Lover of alternative genres', 'default_user.png', 'Active'),
    ('indiefolk', 'Indie Folk Musician', 'indiefolk@example.com', 'indiefolk2023', 'Indie folk artist', 'default_user.png', 'Active'),
    ('tangodancer', 'Tango Dance Lover', 'tangodancer@example.com', 'tangodancer2023', 'Passionate about Tango', 'default_user.png', 'Active'),
    ('edmstar', 'EDM Star Artist', 'edmstar@example.com', 'edmstar2023', 'EDM superstar', 'default_user.png', 'Active'),
    ('acousticfan', 'Acoustic Music Fan', 'acousticfan@example.com', 'acousticfan2023', 'Acoustic music only', 'default_user.png', 'Active'),
    ('latindiva', 'Latin Diva Singer', 'latindiva@example.com', 'latindiva2023', 'Latin music is life', 'default_user.png', 'Active'),
    ('swingmaster', 'Swing Master Dancer', 'swingmaster@example.com', 'swingmaster2023', 'Swing dance enthusiast', 'default_user.png', 'Active'),
    ('classicpiano', 'Classical Piano Artist', 'classicpiano@example.com', 'classicpiano2023', 'Classical piano lover', 'default_user.png', 'Active'),
    ('dubstepking', 'Dubstep King Artist', 'dubstepking@example.com', 'dubstepking2023', 'Dubstep and drops', 'default_user.png', 'Active'),
    ('bluesmaster', 'Blues Music Master', 'bluesmaster@example.com', 'bluesmaster2023', 'Blues all the way', 'default_user.png', 'Active'),
    ('latinbeats', 'Latin Beats Music', 'latinbeats@example.com', 'latinbeats2023', 'Dancing to Latin beats', 'default_user.png', 'Active'),
    ('moondancer', 'Moon Dance Artist', 'moondancer@example.com', 'moondancer2023', 'Dancing under the stars', 'default_user.png', 'Active'),
    ('trapmaster', 'Trap Music Master', 'trapmaster@example.com', 'trapmaster2023', 'Trap music and vibes', 'default_user.png', 'Active'),
    ('folkmagic', 'Folk Music Magic', 'folkmagic@example.com', 'folkmagic2023', 'Folk traditions', 'default_user.png', 'Active'),
    ('electricblues', 'Electric Blues Music', 'electricblues@example.com', 'electricblues2023', 'Electric blues lover', 'default_user.png', 'Active'),
    ('afrobeatking', 'Afrobeat King Music', 'afrobeatking@example.com', 'afrobeatking2023', 'Afrobeat vibes', 'default_user.png', 'Active'),
    ('bassqueen', 'Bass Queen Music', 'bassqueen@example.com', 'bassqueen2023', 'Bass guitar and groove', 'default_user.png', 'Active'),
    ('electrovibe', 'Electro Vibe Music', 'electrovibe@example.com', 'electrovibe2023', 'Electro sounds', 'default_user.png', 'Active'),
    ('raggagirl', 'Ragga Girl Artist', 'raggagirl@example.com', 'raggagirl2023', 'Ragga and reggae vibes', 'default_user.png', 'Active'),
    ('discoqueen', 'Disco Queen Dancer', 'discoqueen@example.com', 'discoqueen2023', 'Disco diva', 'default_user.png', 'Active'),
    ('latinoheat', 'Latino Heat Music', 'latinoheat@example.com', 'latinoheat2023', 'Latin music and dance', 'default_user.png', 'Active'),
    ('afrojazz', 'Afro Jazz Music', 'afrojazz@example.com', 'afrojazz2023', 'Afro Jazz fusion', 'default_user.png', 'Active'),
    ('kpopfan', 'KPop Music Fan', 'kpopfan@example.com', 'kpopfan2023', 'K-Pop for life', 'default_user.png', 'Active'),
    ('bollydance', 'Bolly Dance Artist', 'bollydance@example.com', 'bollydance2023', 'Bollywood dance styles', 'default_user.png', 'Active');


INSERT INTO artist (artist_id, rating)
VALUES 
    (2, 4.5),    -- Salsa Dancer
    (3, 3.2),    -- Techno Beat
    (5, 4.0),    -- Classy Cat
    (7, 4.8),    -- DJ Night
    (8, 2.9),    -- Reggae Vibes
    (9, 3.6),    -- Swing Star
    (11, 4.7),   -- Violin Virtuoso
    (12, 2.1),   -- Bass Player
    (14, 1.5),   -- Metal Head
    (15, 3.8),   -- Jazz Cat
    (16, 5.0),   -- Pop Queen
    (18, 1.2),   -- Rave King
    (20, 3.3),   -- Latin Lover
    (25, 4.9),   -- Dancer Pro
    (28, 0.7),   -- Soul Brother
    (30, 2.3),   -- RnB Vibes
    (31, 3.9),   -- Sax Player
    (32, 4.4),   -- Bollywood Star
    (33, 4.1),   -- Flamenco Queen
    (35, 2.5),   -- EDM Lover
    (37, 3.0),   -- Urban Vibes
    (40, 1.8),   -- Mambo King
    (45, 4.6),   -- Country Boy
    (50, 0.9),   -- Lounge Cat
    (55, 2.7),   -- Gospel Gal
    (60, 3.5),   -- Disco Fever
    (61, 2.2),   -- Pianist Pro
    (65, 4.3),   -- Rock God
    (70, 4.0),   -- Funk Master
    (72, 3.1),   -- Soul Queen
    (75, 4.8);   -- Swing Pro

INSERT INTO admin (email, password)
VALUES 
    ('edgar@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin2@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin3@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin4@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin5@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin6@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin7@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin8@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin9@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin10@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin11@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin12@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin13@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin14@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin15@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin16@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin17@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin18@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin19@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO'),
    ('admin20@example.com', '$2y$10$tDb8S937SDi.v1FBEj.VUuGTF7Nql20pSbKJewnIPnO2aF4q091EO');


INSERT INTO following (artist_id, member_id)
VALUES 
    -- Members following artists
    (2, 3),    -- Techno Beat follows Salsa Dancer
    (2, 5),    -- Classy Cat follows Salsa Dancer
    (7, 8),    -- Reggae Vibes follows DJ Night
    (8, 9),    -- Swing Star follows Reggae Vibes
    (11, 12),  -- Bass Player follows Violin Virtuoso
    (15, 16),  -- Pop Queen follows Jazz Cat
    (18, 20),  -- Latin Lover follows Rave King
    (25, 28),  -- Soul Brother follows Dancer Pro
    (32, 35),  -- EDM Lover follows Bollywood Star
    (37, 40),  -- Mambo King follows Urban Vibes
    (45, 50),  -- Lounge Cat follows Country Boy
    (55, 60),  -- Disco Fever follows Gospel Gal
    (61, 65),  -- Rock God follows Pianist Pro
    (72, 75),  -- Swing Pro follows Soul Queen

    -- Artists following other artists
    (3, 2),    -- Salsa Dancer follows Techno Beat
    (7, 3),    -- Techno Beat follows DJ Night
    (11, 5),   -- Classy Cat follows Violin Virtuoso
    (8, 7),    -- DJ Night follows Reggae Vibes
    (9, 11),   -- Violin Virtuoso follows Swing Star
    (16, 15),  -- Jazz Cat follows Pop Queen
    (20, 18),  -- Rave King follows Latin Lover
    (28, 25),  -- Dancer Pro follows Soul Brother
    (35, 32),  -- Bollywood Star follows EDM Lover
    (40, 37),  -- Urban Vibes follows Mambo King
    (50, 45),  -- Country Boy follows Lounge Cat
    (60, 55),  -- Gospel Gal follows Disco Fever
    (65, 61),  -- Pianist Pro follows Rock God
    (75, 72);  -- Soul Queen follows Swing Pro

INSERT INTO event (event_name, event_date, location, description, refund, price, type_of_event, rating, artist_id, capacity, event_media) 
VALUES
    ('Salsa Night Fever', NOW() - INTERVAL '5 days', 'Downtown Dance Hall', 'A night filled with salsa music and dance performances.', 50.00, 20.00, 'Public', 4.5, 2, 50, 'default_event.png'),
    ('Tech Beats Bash', NOW() - INTERVAL '7 days', 'City Club', 'An electrifying night with top techno beats and live DJs.', 40.00, 25.00, 'Public', 4.0, 3, 100, 'default_event.png'),
    ('Classical Harmony', NOW() - INTERVAL '10 days', 'Grand Symphony Hall', 'An evening of classical music with renowned violinists and orchestras.', 60.00, 50.00, 'Public', 4.8, 11, 150, 'default_event.png'),
    ('DJ Night Live', NOW() - INTERVAL '8 days', 'Nightlife Arena', 'A high-energy DJ night with top music hits from various genres.', 50.00, 30.00, 'Public', 4.7, 7, 200, 'default_event.png'),
    ('Reggae Beach Party', NOW() - INTERVAL '12 days', 'Beachside Stage', 'Chill out with reggae vibes by the beach, featuring local artists.', 35.00, 15.00, 'Public', 4.4, 8, 250, 'default_event.png'),
    ('Swing Dance Gala', NOW() - INTERVAL '15 days', 'Swing Studio', 'A gala event celebrating swing dance with live music.', 70.00, 35.00, 'Private', 4.6, 9, 300, 'default_event.png'),
    ('Metal Madness', NOW() + INTERVAL '20 days', 'Underground Club', 'An intense metal music experience with top bands and performers.', 50.00, 20.00, 'Public', 2.9, 14, 350, 'default_event.png'),
    ('Violin Virtuoso', NOW() + INTERVAL '18 days', 'Concert Hall', 'Experience a night of beautiful violin performances.', 65.00, 45.00, 'Public', 4.7, 11, 400, 'default_event.png'),
    ('Jazz Night', NOW() + INTERVAL '13 days', 'Riverside Amphitheater', 'A smooth night of jazz under the stars.', 55.00, 25.00, 'Public', 3.8, 15, 450, 'default_event.png'),
    ('Pop Fiesta', NOW() + INTERVAL '25 days', 'City Park', 'A colorful pop music festival with live performances.', 70.00, 40.00, 'Public', 5.0, 16, 500, 'default_event.png'),
    ('Latin Dance Extravaganza', NOW() + INTERVAL '22 days', 'Latin Lounge', 'A celebration of Latin dance with performances and open floor dancing.', 60.00, 30.00, 'Public', 4.5, 20, 550, 'default_event.png'),
    ('EDM Explosion', NOW() + INTERVAL '27 days', 'Electric Arena', 'Top EDM DJs performing live for an unforgettable night.', 80.00, 45.00, 'Public', 4.9, 35, 600, 'default_event.png'),
    ('Bollywood Bash', NOW() + INTERVAL '30 days', 'Bollywood Palace', 'An energetic Bollywood night with live performances.', 50.00, 20.00, 'Private', 4.1, 32, 650, 'default_event.png'),
    ('Soul and Funk Groove', NOW() + INTERVAL '17 days', 'Groove Station', 'Get down with the best in soul and funk music.', 45.00, 20.00, 'Public', 3.6, 28, 700, 'default_event.png'),
    ('Country Night Out', NOW() + INTERVAL '10 days', 'Country Barn', 'Enjoy a night of country music with local bands.', 40.00, 25.00, 'Public', 4.4, 45, 750, 'default_event.png'),
    ('Urban Dance Fest', NOW() + INTERVAL '19 days', 'Urban Arena', 'A festival of urban dance styles with battles and performances.', 55.00, 30.00, 'Public', 4.2, 37, 800, 'default_event.png'),
    ('Disco Fever', NOW() + INTERVAL '24 days', 'Disco Lounge', 'Dance the night away with groovy disco music.', 35.00, 15.00, 'Private', 4.0, 60, 850, 'default_event.png'),
    ('Piano Serenade', NOW() + INTERVAL '11 days', 'Piano Hall', 'A serene evening of classical piano music.', 65.00, 35.00, 'Public', 4.3, 61, 900, 'default_event.png'),
    ('Rock Fest', NOW() + INTERVAL '9 days', 'Rock Arena', 'A thrilling rock music festival with live bands.', 50.00, 30.00, 'Public', 4.8, 65, 950, 'default_event.png'),
    ('Swing Soiree', NOW() + INTERVAL '14 days', 'Swing Hall', 'A refined swing dance soiree with live jazz music.', 45.00, 20.00, 'Private', 4.6, 75, 1000, 'default_event.png'),
    ('Afrobeat Summer Jam', NOW() + INTERVAL '16 days', 'Sunset Beach', 'A high-energy Afrobeat festival by the beach.', 60.00, 25.00, 'Public', 4.5, 75, 1050, 'default_event.png'),
    ('Folk Fest', NOW() + INTERVAL '18 days', 'Green Field', 'An outdoor festival celebrating folk music traditions.', 50.00, 20.00, 'Public', 3.9, 28, 1100, 'default_event.png'),
    ('Dubstep Underground', NOW() + INTERVAL '21 days', 'Warehouse District', 'An intense night of dubstep with top DJs from around the world.', 70.00, 35.00, 'Public', 4.7, 75, 1150, 'default_event.png'),
    ('Classical Morning Bliss', NOW() + INTERVAL '23 days', 'Open Garden Theater', 'A morning of classical music to start the day on a peaceful note.', 45.00, 20.00, 'Public', 4.8, 61, 1200, 'default_event.png'),
    ('Latin Fiesta', NOW() + INTERVAL '26 days', 'Latin City Lounge', 'A lively Latin dance party with salsa, bachata, and merengue.', 55.00, 30.00, 'Public', 4.2, 20, 1250, 'default_event.png'),
    ('Jazz Fusion Nights', NOW() + INTERVAL '29 days', 'Downtown Jazz Club', 'A night of jazz fusion featuring the latest sounds and trends.', 50.00, 25.00, 'Private', 3.8, 15, 1300, 'default_event.png'),
    ('Blues on the Bayou', NOW() + INTERVAL '20 days', 'Bayou Stage', 'An evening of blues by the bayou, with the finest blues bands.', 40.00, 20.00, 'Public', 4.3, 55, 1350, 'default_event.png'),
    ('Bollywood Beats', NOW() + INTERVAL '32 days', 'Bollywood Hall', 'Celebrate Bollywood music and dance with live performances.', 50.00, 20.00, 'Private', 4.1, 32, 1400, 'default_event.png'),
    ('Electronic Wave', NOW() + INTERVAL '34 days', 'Electro Dome', 'An electrifying EDM night with top artists and stunning visuals.', 80.00, 45.00, 'Public', 4.9, 35, 1450, 'default_event.png'),
    ('Hip Hop Showcase', NOW() + INTERVAL '15 days', 'Urban Block', 'A showcase of hip hop talent with breakdancers and rap battles.', 30.00, 15.00, 'Public', 3.5, 14, 1500, 'default_event.png'),
    ('Neo Soul Groove', NOW() + INTERVAL '10 days', 'The Groove Lounge', 'A soulful evening featuring neo-soul music and R&B vibes.', 50.00, 25.00, 'Public', 4.6, 28, 1550, 'default_event.png'),
    ('Tribal Beats Night', NOW() + INTERVAL '14 days', 'Jungle Stage', 'Experience tribal rhythms and percussion like never before.', 60.00, 30.00, 'Public', 4.4, 60, 1600, 'default_event.png'),
    ('Opera Under the Stars', NOW() + INTERVAL '22 days', 'Open-Air Opera House', 'An enchanting night of opera performances under the night sky.', 75.00, 50.00, 'Public', 4.9, 61, 1650, 'default_event.png'),
    ('Country Fair', NOW() + INTERVAL '28 days', 'Rustic Barn', 'Enjoy country music, line dancing, and delicious barbecue.', 45.00, 20.00, 'Public', 4.2, 45, 1700, 'default_event.png'),
    ('Rock and Roll Revival', NOW() + INTERVAL '12 days', 'Retro Arena', 'Step back in time with classic rock and roll hits.', 40.00, 15.00, 'Public', 4.3, 65, 1750, 'default_event.png'),
    ('Ambient Chill', NOW() + INTERVAL '16 days', 'The Zen Garden', 'A relaxing ambient music experience in a tranquil setting.', 30.00, 10.00, 'Private', 4.5, 70, 1800, 'default_event.png'),
    ('World Music Fest', NOW() + INTERVAL '31 days', 'Global Stage', 'A celebration of world music, with artists from various cultures.', 55.00, 30.00, 'Public', 4.7, 75, 1850, 'default_event.png'),
    ('Ska Skank', NOW() + INTERVAL '25 days', 'City Center Stage', 'An upbeat night of ska music and lively dancing.', 35.00, 15.00, 'Public', 4.0, 9, 1900, 'default_event.png'),
    ('Electronic Sunrise', NOW() + INTERVAL '27 days', 'Oceanfront Club', 'Dance through the night and watch the sunrise to electronic beats.', 65.00, 35.00, 'Public', 4.6, 35, 1950, 'default_event.png'),
    ('Zumba Fiesta', NOW() + INTERVAL '20 days', 'Fitness Arena', 'A Zumba dance party with energetic Latin beats.', 30.00, 15.00, 'Private', 4.3, 20, 2000, 'default_event.png'),
    ('Psytrance Universe', NOW() + INTERVAL '35 days', 'Cosmic Hall', 'A night of psychedelic trance music and mesmerizing visuals.', 70.00, 40.00, 'Public', 4.8, 75, 2050, 'default_event.png'),
    ('Soulful Sunday', NOW() + INTERVAL '13 days', 'Soulful Sounds Studio', 'Spend a relaxed Sunday with smooth and soulful tunes.', 25.00, 15.00, 'Public', 4.4, 16, 2100, 'default_event.png'),
    ('Reggaeton Rumble', NOW() + INTERVAL '19 days', 'Dance Block', 'Dance to the hottest reggaeton beats with live DJs.', 45.00, 20.00, 'Public', 4.5, 37, 2150, 'default_event.png'),
    ('Indie Acoustic Evening', NOW() + INTERVAL '24 days', 'Indie Café', 'An intimate acoustic performance by top indie artists.', 35.00, 15.00, 'Private', 4.1, 32, 2200, 'default_event.png'),
    ('Flamenco Fire', NOW() + INTERVAL '11 days', 'Flamenco Lounge', 'Feel the passion of flamenco with live performances.', 50.00, 25.00, 'Public', 4.7, 33, 2250, 'default_event.png'),
    ('Electronic Symphony', NOW() + INTERVAL '26 days', 'Symphony Hall', 'A fusion of electronic music and classical instruments.', 75.00, 40.00, 'Public', 4.9, 61, 2300, 'default_event.png'),
    ('Afro-Cuban Salsa Night', NOW() + INTERVAL '29 days', 'Cuban Lounge', 'A night dedicated to Afro-Cuban salsa and rhythmic beats.', 55.00, 25.00, 'Public', 4.6, 2, 2350, 'default_event.png'),
    ('Lo-Fi Chillout', NOW() + INTERVAL '17 days', 'Downtown Café', 'Relax with mellow lo-fi beats in a cozy café setting.', 20.00, 10.00, 'Private', 4.3, 70, 2400, 'default_event.png'),
    ('Bluegrass Bonanza', NOW() + INTERVAL '21 days', 'Country Barn', 'A fun-filled evening of bluegrass music and dance.', 40.00, 15.00, 'Public', 4.2, 45, 2450, 'default_event.png'),
    ('Hard Rock Havoc', NOW() + INTERVAL '33 days', 'Rock City Arena', 'A powerful night of hard rock music with top bands.', 60.00, 35.00, 'Public', 4.5, 65, 2500, 'default_event.png');

INSERT INTO comment (text, comment_date, event_id, member_id, response_comment_id)
VALUES 
    -- Positive Comments
    ('Absolutely loved the energy at Salsa Night Fever! Can’t wait for the next one.', NOW(), 1, 5, NULL),
    ('Tech Beats Bash was insane! The DJs were top-notch.', NOW(), 2, 6, NULL),
    ('Classical Harmony was truly breathtaking. Amazing performances.', NOW(), 3, 11, NULL),
    ('DJ Night Live had such great vibes. Everyone was dancing!', NOW(), 4, 7, NULL),
    ('The beach vibe at Reggae Beach Party was perfect. So relaxing!', NOW(), 5, 8, NULL),
    ('Swing Dance Gala was so much fun! Highly recommend.', NOW(), 6, 12, NULL),
    ('Metal Madness rocked! My ears are still ringing, in the best way.', NOW(), 7, 14, NULL),
    ('Jazz Night was smooth and unforgettable. A great evening.', NOW(), 9, 15, NULL),
    ('Pop Fiesta was a colorful blast! Loved every minute.', NOW(), 10, 16, NULL),
    ('The Afrobeat Summer Jam was full of energy and great music.', NOW(), 21, 28, NULL),
    ('Folk Fest brought such a sense of community. Lovely event.', NOW(), 22, 30, NULL),
    ('Disco Fever was a nostalgic trip. Great music and dance!', NOW(), 27, 60, NULL),
    ('Lo-Fi Chillout at the café was so relaxing, loved the ambiance.', NOW(), 38, 70, NULL),
    ('Hard Rock Havoc had some incredible bands. Definitely coming back.', NOW(), 40, 65, NULL),
    ('The sunrise view at Electronic Sunrise was amazing!', NOW(), 31, 35, NULL),
    -- Negative Comments
    ('The event was too crowded. Could barely move on the dance floor.', NOW(), 1, 3, NULL),
    ('Tech Beats Bash was too loud and overwhelming. Not my vibe.', NOW(), 2, 5, NULL),
    ('Classical Harmony was okay, but I expected a larger orchestra.', NOW(), 3, 8, NULL),
    ('Reggae Beach Party was great, but it started too late.', NOW(), 5, 9, NULL),
    ('Metal Madness was too chaotic. Sound quality could have been better.', NOW(), 7, 10, NULL),
    ('Swing Dance Gala was nice, but the venue was cramped.', NOW(), 6, 12, NULL),
    ('Pop Fiesta was fun, but the ticket price was too high.', NOW(), 10, 15, NULL),
    ('Afrobeat Summer Jam had a great lineup, but it was poorly organized.', NOW(), 21, 18, NULL),
    ('Folk Fest was nice, but the sound system kept failing.', NOW(), 22, 28, NULL),
    ('The event location for Disco Fever was too hard to find.', NOW(), 27, 32, NULL),
    -- Replies to previous comments (Positive responses)
    ('Glad you enjoyed Salsa Night Fever! We had an amazing time organizing it.', NOW(), 1, 6, 1),
    ('The DJs loved the energy at Tech Beats Bash too! Thanks for coming.', NOW(), 2, 7, 2),
    ('So happy you liked Classical Harmony. We aim for a magical experience.', NOW(), 3, 11, 3),
    ('Totally agree! Reggae Beach Party was all about the beach vibe.', NOW(), 5, 8, 5),
    ('Swing Dance Gala was a blast! Great crowd energy.', NOW(), 6, 9, 6),
    -- Replies to previous comments (Negative responses)
    ('Sorry you found it crowded, we’re working on better crowd control.', NOW(), 1, 5, 17),
    ('We apologize for the sound quality at Metal Madness. We’re addressing it.', NOW(), 7, 14, 21),
    ('Ticket prices for Pop Fiesta will be reviewed for next time. Thank you!', NOW(), 10, 16, 24),
    ('We’re working on improving our event organization. Thanks for your feedback.', NOW(), 21, 20, 27),
    ('We’ll make sure Disco Fever is easier to find next time. Appreciate the note!', NOW(), 27, 15, 29);

INSERT INTO tag (tag_type, tag_name, color)
VALUES 
    ('Music', 'Music', 'D32F2F'),       -- Crimson for Music
    ('Dance', 'Dance', '5A4FCF'),       -- Medium Purple for Dance
    -- Music Genres
    ('Music', 'Jazz', 'CC9900'),        -- Dark Gold for Jazz
    ('Music', 'Rock', '8B0000'),        -- Crimson Red for Rock
    ('Music', 'Classical', 'A3C18D'),   -- Muted Green for Classical
    ('Music', 'Electronic', '9B2C37'), -- Muted Burgundy for Electronic
    ('Music', 'HipHop', '482A5F'),      -- Deep Purple for HipHop
    ('Music', 'Metal', '8B1A1A'),       -- Burgundy for Metal
    ('Music', 'Reggae', 'E35C29'),      -- Burnt Orange for Reggae
    ('Music', 'Latin', 'BF40BF'),       -- Deep Magenta for Latin
    ('Music', 'Pop', 'E75887'),         -- Rose Pink for Pop
    ('Music', 'Blues', '2F2F2F'),       -- Dark Gray for Blues
    ('Music', 'Soul', 'CCB400'),        -- Mustard Yellow for Soul
    ('Music', 'Indie', '8751A7'),       -- Rich Purple for Indie
    ('Music', 'Folk', 'D84512'),        -- Burnt Orange for Folk
    ('Music', 'Psytrance', '009FAE'),   -- Teal for Psytrance
    ('Music', 'NeoSoul', '5A49A8'),     -- Rich Indigo for Neo Soul
    ('Music', 'Country', '9E732B'),     -- Muted Gold for Country
    ('Music', 'Afrobeat', 'E36E50'),    -- Deep Coral for Afrobeat
    ('Music', 'LoFi', '647980'),        -- Slate Blue for Lo-Fi
    ('Music', 'EDM', '00BF6F'),         -- Emerald Green for EDM
    ('Music', 'Dubstep', '3A216F'),     -- Deep Indigo for Dubstep
    -- Dance Styles
    ('Dance', 'Salsa', 'D13F2F'),       -- Vibrant Red for Salsa
    ('Dance', 'Ballet', 'D96C6C'),      -- Deep Coral for Ballet
    ('Dance', 'Tango', 'C82333'),       -- Scarlet for Tango
    ('Dance', 'HipHopDance', 'E05C3F'), -- Vibrant Orange for Hip Hop Dance
    ('Dance', 'Ballroom', '5F3EA8'),    -- Rich Violet for Ballroom
    ('Dance', 'Contemporary', '198F8F'),-- Dark Cyan for Contemporary
    ('Dance', 'Breakdance', '356B92'),  -- Slate Blue for Breakdance
    ('Dance', 'SwingDance', '3F808A'),  -- Muted Aqua for Swing Dance
    ('Dance', 'TapDance', '7A4E34'),    -- Brown for Tap Dance
    ('Dance', 'Flamenco', 'B52782'),    -- Deep Pink for Flamenco
    ('Dance', 'Zumba', '91A931'),       -- Olive for Zumba
    ('Dance', 'Bollywood', 'D1533F'),   -- Bright Red-Orange for Bollywood Dance
    ('Dance', 'JazzDance', 'CCA300'),   -- Gold for Jazz Dance
    ('Dance', 'Waltz', '7199B0'),       -- Soft Blue for Waltz
    ('Dance', 'Samba', 'E03C2F'),       -- Bright Scarlet for Samba
    ('Dance', 'AfroCuban', 'C7462F'),   -- Deep Orange for Afro-Cuban
    ('Dance', 'StreetDance', '2F7552'), -- Forest Green for Street Dance
    ('Dance', 'LatinDance', 'DB3A83'),  -- Hot Pink for Latin Dance
    ('Dance', 'Foxtrot', 'B45555'),     -- Rosewood for Foxtrot
    ('Dance', 'Quickstep', '238C8A'),   -- Cyan for Quickstep
    -- Event Settings
    ('Settings', 'Outdoors', '23672A'), -- Dark Forest Green for Outdoors
    ('Settings', 'Indoors', '545454'),  -- Medium Gray for Indoors
    ('Settings', 'Beach', '4A90B0'),    -- Ocean Blue for Beach
    ('Settings', 'Garden', '3A8C3A'),   -- Rich Green for Garden
    ('Settings', 'Park', '6BB06B'),     -- Lush Green for Park
    ('Settings', 'Lounge', 'A0835A'),   -- Tan for Lounge
    ('Settings', 'Rooftop', '3C6075'),  -- Deep Blue for Rooftop
    ('Settings', 'Club', '1E1E1E'),     -- Charcoal for Club
    ('Settings', 'Amphitheater', '5C5C5C'), -- Steel Gray for Amphitheater
    ('Settings', 'Barn', '704B2C'),     -- Rich Brown for Barn
    ('Settings', 'Arena', '3E4E4E'),    -- Slate Gray for Arena
    ('Settings', 'Hall', '546875'),     -- Muted Blue for Hall
    ('Settings', 'Studio', '7088A0'),   -- Soft Steel Blue for Studio
    ('Settings', 'Festival', 'B28A00'), -- Dark Yellow for Festival
    ('Settings', 'Ballroom', '8652B3'), -- Plum for Ballroom
    ('Settings', 'Theater', '7E51A8'),  -- Medium Purple for Theater
    -- Event Moods
    ('Mood', 'Energetic', 'E03F2F'),    -- Bright Red for Energetic
    ('Mood', 'Relaxed', '4A90B0'),      -- Calm Blue for Relaxed
    ('Mood', 'Romantic', 'DB3A83'),     -- Hot Pink for Romantic
    ('Mood', 'Mellow', '9A6D6D'),       -- Soft Brown for Mellow
    ('Mood', 'Vibrant', 'D8452E'),      -- Bright Orange for Vibrant
    ('Mood', 'Intimate', '5F3EA8'),     -- Rich Indigo for Intimate
    ('Mood', 'HighEnergy', '20999F'),   -- Bright Teal for High Energy
    ('Mood', 'Sophisticated', '5F7D99'),-- Muted Blue for Sophisticated
    ('Mood', 'Party', 'B03F8B'),        -- Deep Magenta for Party
    ('Mood', 'Chill', '505E66'),        -- Cool Gray for Chill
    ('Mood', 'Cultural', 'A66933'),     -- Muted Gold for Cultural
    ('Mood', 'Nostalgic', 'B08530'),    -- Rich Goldenrod for Nostalgic
    ('Mood', 'Exotic', '8A552E');       -- Warm Brown for Exotic



INSERT INTO event_tag (event_id, tag_id)
VALUES 
    -- Salsa Night Fever
    (1, 22 + 2),     -- Salsa
    (1, 41 + 2),     -- Indoors
    (1, 51 + 2),     -- Energetic
    (1, 1),          -- Music
    (1, 2),          -- Dance
    -- Tech Beats Bash
    (2, 4 + 2),      -- Electronic
    (2, 48 + 2),     -- Club
    (2, 53 + 2),     -- HighEnergy
    (2, 1),          -- Music
    -- Classical Harmony
    (3, 3 + 2),      -- Classical
    (3, 60 + 2),     -- Sophisticated
    (3, 47 + 2),     -- Hall
    (3, 1),          -- Music
    -- DJ Night Live
    (4, 1 + 2),      -- HipHop
    (4, 4 + 2),      -- Electronic
    (4, 48 + 2),     -- Club
    (4, 51 + 2),     -- Energetic
    (4, 2),          -- Dance
    -- Reggae Beach Party
    (5, 7 + 2),      -- Reggae
    (5, 42 + 2),     -- Beach
    (5, 55 + 2),     -- Chill
    (5, 1),          -- Music
    -- Swing Dance Gala
    (6, 25 + 2),     -- SwingDance
    (6, 57 + 2),     -- Theater
    (6, 51 + 2),     -- Energetic
    (6, 1),          -- Music
    (6, 2),          -- Dance
    -- Metal Madness
    (7, 6 + 2),      -- Metal
    (7, 47 + 2),     -- Arena
    (7, 53 + 2),     -- HighEnergy
    (7, 1),          -- Music
    -- Violin Virtuoso
    (8, 3 + 2),      -- Classical
    (8, 60 + 2),     -- Sophisticated
    (8, 59 + 2),     -- Intimate
    (8, 1),          -- Music
    -- Jazz Night
    (9, 1 + 2),      -- Jazz
    (9, 57 + 2),     -- Theater
    (9, 54 + 2),     -- Mellow
    (9, 1),          -- Music
    -- Pop Fiesta
    (10, 9 + 2),     -- Pop
    (10, 45 + 2),    -- Outdoors
    (10, 51 + 2),    -- Energetic
    (10, 52 + 2),    -- Vibrant
    (10, 1),         -- Music
    -- Afrobeat Summer Jam
    (21, 17 + 2),    -- Afrobeat
    (21, 45 + 2),    -- Outdoors
    (21, 55 + 2),    -- Chill
    (21, 1),         -- Music
    (21, 2),         -- Dance
    -- Folk Fest
    (22, 13 + 2),    -- Folk
    (22, 45 + 2),    -- Outdoors
    (22, 56 + 2),    -- Cultural
    (22, 1),         -- Music
    -- Disco Fever
    (27, 9 + 2),     -- Pop
    (27, 48 + 2),    -- Club
    (27, 53 + 2),    -- HighEnergy
    (27, 1),         -- Music
    (27, 2),         -- Dance
    -- Lo-Fi Chillout
    (38, 18 + 2),    -- LoFi
    (38, 50 + 2),    -- Lounge
    (38, 54 + 2),    -- Mellow
    (38, 1),         -- Music
    -- Hard Rock Havoc
    (40, 2 + 2),     -- Rock
    (40, 47 + 2),    -- Arena
    (40, 53 + 2),    -- HighEnergy
    (40, 1),         -- Music
    -- Ambient Chill
    (41, 18 + 2),    -- LoFi
    (41, 42 + 2),    -- Garden
    (41, 55 + 2),    -- Chill
    (41, 1),         -- Music
    -- Opera Under the Stars
    (42, 3 + 2),     -- Classical
    (42, 45 + 2),    -- Outdoors
    (42, 60 + 2),    -- Sophisticated
    (42, 1),         -- Music
    -- Country Fair
    (43, 16 + 2),    -- Country
    (43, 46 + 2),    -- Park
    (43, 56 + 2),    -- Cultural
    (43, 1),         -- Music
    -- Tribal Beats Night
    (44, 17 + 2),    -- Afrobeat
    (44, 42 + 2),    -- Garden
    (44, 53 + 2),    -- HighEnergy
    (44, 1),         -- Music
    (44, 2),         -- Dance
    -- Zumba Fiesta
    (45, 32 + 2),    -- Zumba
    (45, 41 + 2),    -- Indoors
    (45, 51 + 2),    -- Energetic
    (45, 2),         -- Dance
    -- Psytrance Universe
    (46, 15 + 2),    -- Psytrance
    (46, 49 + 2),    -- Rooftop
    (46, 52 + 2),    -- Vibrant
    (46, 1),         -- Music
    (46, 2),         -- Dance
    -- Flamenco Fire
    (47, 29 + 2),    -- Flamenco
    (47, 57 + 2),    -- Theater
    (47, 59 + 2),    -- Intimate
    (47, 1),         -- Music
    (47, 2),         -- Dance
    -- Neo Soul Groove
    (48, 12 + 2),    -- Soul
    (48, 50 + 2),    -- Lounge
    (48, 54 + 2),    -- Mellow
    (48, 1),         -- Music
    -- Blues on the Bayou
    (49, 10 + 2),    -- Blues
    (49, 42 + 2),    -- Garden
    (49, 56 + 2),    -- Cultural
    (49, 1),         -- Music
    -- Bollywood Beats
    (50, 34 + 2),    -- Bollywood
    (50, 41 + 2),    -- Indoors
    (50, 52 + 2),    -- Vibrant
    (50, 1),         -- Music
    (50, 2);         -- Dance



INSERT INTO ticket (event_id, ticket_date, member_id)
VALUES -- Tickets for different events
    (1, NOW(), 3),    -- Member 3 has a ticket for Salsa Night Fever
    (2, NOW(), 5),    -- Member 5 has a ticket for Tech Beats Bash
    (3, NOW(), 8),    -- Member 8 has a ticket for Classical Harmony
    (4, NOW(), 10),   -- Member 10 has a ticket for DJ Night Live
    (5, NOW(), 12),   -- Member 12 has a ticket for Reggae Beach Party
    (6, NOW(), 15),   -- Member 15 has a ticket for Swing Dance Gala
    (7, NOW(), 18),   -- Member 18 has a ticket for Metal Madness
    (8, NOW(), 20),   -- Member 20 has a ticket for Violin Virtuoso
    (9, NOW(), 22),   -- Member 22 has a ticket for Jazz Night
    (10, NOW(), 25),  -- Member 25 has a ticket for Pop Fiesta
    (21, NOW(), 28),  -- Member 28 has a ticket for Afrobeat Summer Jam
    (22, NOW(), 30),  -- Member 30 has a ticket for Folk Fest
    (27, NOW(), 32),  -- Member 32 has a ticket for Disco Fever
    (38, NOW(), 35),  -- Member 35 has a ticket for Lo-Fi Chillout
    (40, NOW(), 37),  -- Member 37 has a ticket for Hard Rock Havoc
    (41, NOW(), 40),  -- Member 40 has a ticket for Ambient Chill
    (42, NOW(), 45),  -- Member 45 has a ticket for Opera Under the Stars
    (43, NOW(), 47),  -- Member 47 has a ticket for Country Fair
    (44, NOW(), 50),  -- Member 50 has a ticket for Tribal Beats Night
    (46, NOW(), 55),  -- Member 55 has a ticket for Psytrance Universe
    (47, NOW(), 58),  -- Member 58 has a ticket for Flamenco Fire
    (48, NOW(), 60),  -- Member 60 has a ticket for Neo Soul Groove
    (49, NOW(), 62),  -- Member 62 has a ticket for Blues on the Bayou
    (50, NOW(), 65),  -- Member 65 has a ticket for Bollywood Beats
    (50, NOW(), 68);  -- Member 68 has a ticket for Indie Acoustic Evening

-- Creating Polls for events
INSERT INTO poll (event_id, start_date, end_date)
VALUES 
    (1, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days'),  -- Poll for Salsa Night Fever
    (5, CURRENT_DATE, CURRENT_DATE + INTERVAL '5 days'),  -- Poll for Reggae Beach Party
    (10, CURRENT_DATE, CURRENT_DATE + INTERVAL '10 days'), -- Poll for Pop Fiesta
    (21, CURRENT_DATE, CURRENT_DATE + INTERVAL '6 days'),  -- Poll for Afrobeat Summer Jam
    (22, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days'),  -- Poll for Folk Fest
    (40, CURRENT_DATE, CURRENT_DATE + INTERVAL '8 days'),  -- Poll for Hard Rock Havoc
    (3, CURRENT_DATE, CURRENT_DATE + INTERVAL '5 days'),  -- Poll for Classical Harmony
    (8, CURRENT_DATE, CURRENT_DATE + INTERVAL '7 days'),  -- Poll for Violin Virtuoso
    (15, CURRENT_DATE, CURRENT_DATE + INTERVAL '6 days'), -- Poll for Jazz Night
    (27, CURRENT_DATE, CURRENT_DATE + INTERVAL '10 days'),-- Poll for Disco Fever
    (38, CURRENT_DATE, CURRENT_DATE + INTERVAL '8 days'); -- Poll for Lo-Fi Chillout

-- Options for each Poll
-- Poll 1: Where would you like the next Salsa Night Fever event?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Beach', 1),
    ('Rooftop', 1),
    ('City Center', 1);
-- Poll 2: What time do you prefer for Reggae Beach Party?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Afternoon', 2),
    ('Evening', 2),
    ('Late Night', 2);
-- Poll 3: Which location do you prefer for Pop Fiesta?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Outdoor Park', 3),
    ('City Square', 3),
    ('Indoor Arena', 3);
-- Poll 4: What theme would you like for Afrobeat Summer Jam?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Tropical', 4),
    ('Urban Jungle', 4);
-- Poll 5: What should be the primary music style for Folk Fest?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Traditional Folk', 5),
    ('Folk Rock', 5),
    ('Country Folk', 5);
-- Poll 6: Which time works best for Hard Rock Havoc?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Evening', 6),
    ('Midnight', 6);
-- Poll 7: What ambiance would you like for Classical Harmony?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Candlelit', 7),
    ('Outdoor Garden', 7),
    ('Concert Hall', 7);
-- Poll 8: Which violinist would you prefer to see perform at Violin Virtuoso?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Joshua Bell', 8),
    ('Itzhak Perlman', 8),
    ('Anne-Sophie Mutter', 8);
-- Poll 9: What jazz style should be featured in Jazz Night?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Bebop', 9),
    ('Free Jazz', 9);
-- Poll 10: What dress code should be encouraged for Disco Fever?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('70s Disco', 10),
    ('Funky Casual', 10),
    ('Formal Glam', 10);
-- Poll 11: What time of day should Lo-Fi Chillout start?
INSERT INTO option (option_name, poll_id)
VALUES 
    ('Afternoon', 11),
    ('Evening', 11);
-- Poll 12: What theme would you like for Afro-Cuban Salsa Night?

    INSERT INTO invitation (invitation_message, invitation_date, event_id, member_id)
VALUES 
    -- Invitations for Salsa Night Fever
    ('You’re invited to join us at Salsa Night Fever! Let’s dance the night away!', NOW(), 1, 5),
    ('Come experience the passion of Salsa Night Fever. It’s going to be an unforgettable night!', NOW(), 1, 8),
    -- Invitations for Tech Beats Bash
    ('Join us at Tech Beats Bash for an electrifying night of techno music!', NOW(), 2, 12),
    ('Ready to rave? Tech Beats Bash is the place to be this weekend!', NOW(), 2, 15),
    -- Invitations for Reggae Beach Party
    ('Feel the vibes at Reggae Beach Party! You won’t want to miss it.', NOW(), 5, 20),
    ('Relax by the beach with reggae tunes. Join us at Reggae Beach Party!', NOW(), 5, 18),
    -- Invitations for Jazz Night
    ('You’re invited to Jazz Night! A perfect evening for jazz lovers.', NOW(), 9, 25),
    ('Join us for a smooth evening at Jazz Night. Great music and great company!', NOW(), 9, 22),
    -- Invitations for Pop Fiesta
    ('Pop Fiesta awaits! Get ready for an energetic night of pop hits.', NOW(), 10, 28),
    ('Let’s party at Pop Fiesta! Join us for a colorful and lively night.', NOW(), 10, 30),
    -- Invitations for Afrobeat Summer Jam
    ('Afrobeat Summer Jam is here! Let’s celebrate with good vibes and music.', NOW(), 21, 35),
    ('You’re invited to Afrobeat Summer Jam! A night of Afrobeat rhythms awaits.', NOW(), 21, 32),
    -- Invitations for Folk Fest
    ('Come be a part of the community at Folk Fest! An event full of tradition and music.', NOW(), 22, 40),
    ('Join us for Folk Fest and enjoy some heartwarming folk music.', NOW(), 22, 45),
    -- Invitations for Disco Fever
    ('Disco Fever is calling! Get your best disco outfit ready and join us!', NOW(), 27, 50),
    ('It’s time to groove at Disco Fever! We can’t wait to see you there.', NOW(), 27, 55),
    -- Invitations for Lo-Fi Chillout
    ('Unwind with us at Lo-Fi Chillout, the perfect way to relax and enjoy.', NOW(), 38, 60),
    ('You’re invited to a mellow evening at Lo-Fi Chillout. See you there!', NOW(), 38, 62),
    -- Invitations for Hard Rock Havoc
    ('Hard Rock Havoc is back! Get ready for an intense night of rock music.', NOW(), 40, 65),
    ('Join us for Hard Rock Havoc and experience the power of rock and roll!', NOW(), 40, 68),
    -- Invitations for Classical Harmony
    ('You’re invited to a magical night at Classical Harmony. Don’t miss it!', NOW(), 3, 70),
    ('Join us for an enchanting evening of classical music at Classical Harmony.', NOW(), 3, 72),
    -- Invitations for Zumba Fiesta
    ('Dance it out at Zumba Fiesta! Get ready for an energetic experience.', NOW(), 45, 75),
    ('You’re invited to Zumba Fiesta! Join us for a fun-filled workout session.', NOW(), 45, 77);
    -- Invitations for Jazz Fusion Nights

-- Voting Data for Updated Polls
INSERT INTO voting (poll_id, option_id, member_id)
VALUES 
    -- Voting on Poll 1: Salsa Night Fever
    (1, 1, 5),    -- Member 5 votes for "Beach"
    (1, 2, 8),    -- Member 8 votes for "Rooftop"
    (1, 3, 12),   -- Member 12 votes for "City Center"
    -- Voting on Poll 2: Reggae Beach Party
    (2, 1, 15),   -- Member 15 votes for "Afternoon"
    (2, 2, 20),   -- Member 20 votes for "Evening"
    (2, 3, 25),   -- Member 25 votes for "Late Night"
    -- Voting on Poll 3: Pop Fiesta
    (3, 1, 28),   -- Member 28 votes for "Outdoor Park"
    (3, 2, 30),   -- Member 30 votes for "City Square"
    (3, 3, 32),   -- Member 32 votes for "Indoor Arena"
    -- Voting on Poll 4: Afrobeat Summer Jam
    (4, 1, 35),   -- Member 35 votes for "Tropical"
    (4, 2, 37),   -- Member 37 votes for "Urban Jungle"
    -- Voting on Poll 5: Folk Fest
    (5, 1, 40),   -- Member 40 votes for "Traditional Folk"
    (5, 2, 45),   -- Member 45 votes for "Folk Rock"
    (5, 3, 50),   -- Member 50 votes for "Country Folk"
    -- Voting on Poll 6: Hard Rock Havoc
    (6, 1, 55),   -- Member 55 votes for "Evening"
    (6, 2, 60),   -- Member 60 votes for "Midnight"
    -- Voting on Poll 7: Classical Harmony
    (7, 1, 65),   -- Member 65 votes for "Candlelit"
    (7, 2, 70),   -- Member 70 votes for "Outdoor Garden"
    (7, 3, 75),   -- Member 75 votes for "Concert Hall"
    -- Voting on Poll 8: Violin Virtuoso
    (8, 1, 8),    -- Member 8 votes for "Joshua Bell"
    (8, 2, 12),   -- Member 12 votes for "Itzhak Perlman"
    (8, 3, 22),   -- Member 22 votes for "Anne-Sophie Mutter"
    -- Voting on Poll 9: Jazz Night
    (9, 1, 25),   -- Member 25 votes for "Bebop"
    (9, 2, 28),   -- Member 28 votes for "Free Jazz"
    -- Voting on Poll 10: Disco Fever
    (10, 1, 30),  -- Member 30 votes for "70s Disco"
    (10, 2, 35),  -- Member 35 votes for "Funky Casual"
    (10, 3, 37),  -- Member 37 votes for "Formal Glam"
    -- Voting on Poll 11: Lo-Fi Chillout
    (11, 1, 40),  -- Member 40 votes for "Afternoon"
    (11, 2, 45);  -- Member 45 votes for "Evening"


-- Ratings for Events by Different Members with Ratings from 0 to 5
INSERT INTO rating (event_id, member_id, rating)
VALUES 
    -- Salsa Night Fever Ratings
    (1, 5, 4.5),    -- Member 5 rates Salsa Night Fever
    (1, 8, 3.0),    -- Member 8 rates Salsa Night Fever
    (1, 12, 5.0),   -- Member 12 rates Salsa Night Fever
    -- Tech Beats Bash Ratings
    (2, 15, 2.8),   -- Member 15 rates Tech Beats Bash
    (2, 20, 4.1),   -- Member 20 rates Tech Beats Bash
    (2, 25, 3.6),   -- Member 25 rates Tech Beats Bash
    -- Classical Harmony Ratings
    (3, 22, 4.9),   -- Member 22 rates Classical Harmony
    (3, 28, 5.0),   -- Member 28 rates Classical Harmony
    (3, 32, 3.2),   -- Member 32 rates Classical Harmony
    -- Reggae Beach Party Ratings
    (5, 37, 4.0),   -- Member 37 rates Reggae Beach Party
    (5, 40, 3.5),   -- Member 40 rates Reggae Beach Party
    (5, 45, 2.7),   -- Member 45 rates Reggae Beach Party
    -- Pop Fiesta Ratings
    (10, 50, 5.0),  -- Member 50 rates Pop Fiesta
    (10, 55, 1.0),  -- Member 55 rates Pop Fiesta
    (10, 60, 3.8),  -- Member 60 rates Pop Fiesta
    -- Afrobeat Summer Jam Ratings
    (21, 65, 4.2),  -- Member 65 rates Afrobeat Summer Jam
    (21, 68, 3.0),  -- Member 68 rates Afrobeat Summer Jam
    (21, 70, 4.9),  -- Member 70 rates Afrobeat Summer Jam
    -- Folk Fest Ratings
    (22, 72, 4.5),  -- Member 72 rates Folk Fest
    (22, 75, 1.5),  -- Member 75 rates Folk Fest
    (22, 77, 0.0),  -- Member 77 rates Folk Fest
    -- Hard Rock Havoc Ratings
    (40, 30, 5.0),  -- Member 30 rates Hard Rock Havoc
    (40, 32, 2.2),  -- Member 32 rates Hard Rock Havoc
    (40, 35, 4.7),  -- Member 35 rates Hard Rock Havoc
    -- Jazz Night Ratings
    (15, 12, 3.4),  -- Member 12 rates Jazz Night
    (15, 22, 4.8),  -- Member 22 rates Jazz Night
    (15, 25, 0.9),  -- Member 25 rates Jazz Night
    -- Disco Fever Ratings
    (27, 28, 5.0),  -- Member 28 rates Disco Fever
    (27, 37, 2.5),  -- Member 37 rates Disco Fever
    (27, 40, 3.1),  -- Member 40 rates Disco Fever
    -- Lo-Fi Chillout Ratings
    (38, 45, 4.2),  -- Member 45 rates Lo-Fi Chillout
    (38, 50, 0.5),  -- Member 50 rates Lo-Fi Chillout
    (38, 55, 3.3);  -- Member 55 rates Lo-Fi Chillout

-- Restrictions applied to members by admins
INSERT INTO restriction (member_id, duration, admin_id, start)
VALUES 
    -- Suspensions (non-zero duration)
    (5, INTERVAL '7 days', 1, NOW()),       -- Member 5 suspended for 7 days by Admin 1
    (8, INTERVAL '30 days', 2, NOW()),      -- Member 8 suspended for 30 days by Admin 2
    (12, INTERVAL '3 days', 3, NOW()),      -- Member 12 suspended for 3 days by Admin 3

    -- Bans (duration of 0 means a ban)
    (15, INTERVAL '0 days', 1, NOW()),      -- Member 15 banned by Admin 1
    (20, INTERVAL '0 days', 2, NOW()),      -- Member 20 banned by Admin 2
    (25, INTERVAL '0 days', 3, NOW());      -- Member 25 banned by Admin 3

INSERT INTO join_request(event_id, member_id, request_date, status)
VALUES 
    (1, 1, NOW() + INTERVAL '1 days', 'Pending'),
    (2, 2, NOW() + INTERVAL '2 days', 'Pending'),
    (3, 3, NOW() + INTERVAL '3 days', 'Approved'),
    (4, 4, NOW() + INTERVAL '4 days', 'Approved'),
    (5, 5, NOW() + INTERVAL '5 days', 'Rejected'),
    (6, 6, NOW() + INTERVAL '6 days', 'Rejected'),
    (7, 7, NOW() + INTERVAL '2 days', 'Pending'),
    (8, 8, NOW() + INTERVAL '2 days', 'Pending'),
    (9, 9, NOW() + INTERVAL '2 days', 'Pending'),
    (10, 10, NOW() + INTERVAL '2 days', 'Pending');


INSERT INTO vote_comment (comment_id, member_id, vote) 
VALUES
        (1, 1, true),
        (2, 2, false),
        (3, 3, true),
        (4, 4, false),
        (5, 5, true),
        (6, 6, true),
        (7, 7, false),
        (8, 8, true),
        (9, 9, false),
        (10, 10, true),
        (11, 11, false),
        (12, 12, true),
        (13, 13, false),
        (14, 14, true),
        (15, 15, true),
        (16, 16, false),
        (17, 17, true),
        (18, 18, false),
        (19, 19, true),
        (20, 20, false);
