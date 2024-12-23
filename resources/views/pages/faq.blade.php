@extends('layouts.app')

@section('content')
<div class="info-page">
    <header>
        <h1>Frequently Asked Questions</h1>
    </header>

    <main>
        <section class="info-content">

        <h2>General Questions</h2>

        <div class="faq-item">
            <h3>1. What is SyncIt!?</h3>
            <p>SyncIt! is a platform where artists in the Music and Dance industries can showcase their work and promote their events to a larger audience. Fans can discover, attend, and engage with events through the platform.</p>
        </div>

        <div class="faq-item">
            <h3>2. How does SyncIt! support artists?</h3>
            <p>SyncIt! allows artists to create and promote events, connect with their audience, and build a community around their passion. Artists can share photos, videos, and event details while receiving feedback and ratings from attendees.</p>
        </div>

        <div class="faq-item">
            <h3>3. Who can use SyncIt!?</h3>
            <p>SyncIt! is open to both artists and fans. Artists can create events, while fans can discover and attend these events. By following artists, fans get notified of new events and updates.</p>
        </div>

        <h2>Event Management</h2>

        <div class="faq-item">
            <h3>4. How do I create an event on SyncIt!?</h3>
            <p>To create an event, log in to your account and navigate to the "Create Event" page. Fill in the required details, such as location, date, time, price, and capacity. You can also add photos, videos, and descriptions to promote your event.</p>
        </div>

        <div class="faq-item">
            <h3>5. Can I create both public and private events?</h3>
            <p>Yes! SyncIt! allows you to create public events that are open to all users, as well as private events that are invite-only. You can manage the guest list for private events to control who can attend.</p>
        </div>

        <div class="faq-item">
            <h3>6. What happens if I need to cancel an event?</h3>
            <p>Artists can cancel events through the event management page. When an event is canceled, all attendees are notified immediately, and if applicable, refunds are processed accordingly.</p>
        </div>

        <div class="faq-item">
            <h3>7. Can I see feedback from attendees after the event?</h3>
            <p>Yes, after the event concludes, attendees can leave feedback in the form of comments and ratings. This feedback is visible to the artist and other attendees under the "Past Events" section.</p>
        </div>

        <div class="faq-item">
            <h3>8. Can artists earn ratings on SyncIt!?</h3>
            <p>Yes, artists can earn a rating based on the average score from attendees who rate their past events. These ratings help showcase the artist's reputation and quality of events.</p>
        </div>

        <h2>Account & Authentication</h2>

        <div class="faq-item">
            <h3>9. How do I create an account on SyncIt!?</h3>
            <p>To create an account, click the "Register" button and fill in your details, such as your name, email, and password.</p>
        </div>

        <div class="faq-item">
            <h3>10. How do I verify my email?</h3>
            <p>After signing up, you will receive a verification email with a link. Click the link to confirm your email address. If you do not receive the email, you can request it to be resent.</p>
        </div>

        <div class="faq-item">
            <h3>11. What if I forget my password?</h3>
            <p>If you forget your password, you can click the "Forgot Password" link on the login page. You'll receive a reset link via email to create a new password.</p>
        </div>

        <div class="faq-item">
            <h3>12. Can I change my profile information?</h3>
            <p>Yes, you can update your profile information by visiting the "Profile" section. Here, you can change your name, email, and other personal details as needed.</p>
        </div>

        <h2>Payments & Refunds</h2>

        <div class="faq-item">
            <h3>13. Can I create free events?</h3>
            <p>Yes, SyncIt! allows you to create both free and paid events. When setting up your event, you can specify if it’s free or set a ticket price for attendees.</p>
        </div>

        <div class="faq-item">
            <h3>14. How do refunds work?</h3>
            <p>Refunds are issued when an event is canceled by the artist. Attendees are notified and refunded directly. The platform facilitates this process to ensure a smooth experience for users.</p>
        </div>

        <h2>Other Questions</h2>

        <div class="faq-item">
            <h3>15. How does SyncIt! ensure a safe community?</h3>
            <p>SyncIt! has a dedicated team of administrators who moderate content and user interactions. Administrators have the authority to suspend or ban users if they violate community guidelines or act inappropriately.</p>
        </div>
        </section>
    </main>
</div>

@endsection
