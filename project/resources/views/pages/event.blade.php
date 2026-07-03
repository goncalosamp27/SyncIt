@extends('layouts.app')

@section('content')
	@if (session('success'))
		<div class = "success">
			{{ session('success') }}
		</div>
	@endif
		@if (session('error'))
		<div class="error">
			{{ session('error') }}
		</div>
	@endif
	<script src="{{ asset('js/app.js') }}" defer></script>
	<script src="{{ asset('js/comment.js') }}" defer></script>
	<script src="{{ asset('js/comment-list.js') }}" defer></script>
	<script>
		const commentUrl = @json(route('comments.store', ['event_id' => $event->event_id]));
		const getCommentsUrl = @json(route('comments.index', ['event_id' => $event->event_id]));
	</script>
	<div id="confirmationModal1" class="new-modal" style="display: none">
		@include('partials.confirm-overlay', [
			'message' => 'Are you sure you want to cancel this event? You will not be able to revert this action.',
			'route' => route('event.cancel', ['event_id' => $event->event_id]),
			'id' => 1
		])
	</div>

	<div id="confirmationModal2" class="new-modal" style="display: {{ $errors->any() ? 'block' : 'none' }};">
		@include('partials.confirm-overlay', [
			'message' => 'Are you sure you want to report this event?',
			'route' => route('create.report', ['event_id' => $event->event_id]),
			'id' => 2
		])
	</div>

	<div class="event-page-content">
	<div class="event-page-img">
			<img src="{{ $event->getEventImage()}}" alt="Event Picture">
		</div>
		<div class="event-page-info">
			<div class="title-edit">
				<h1>
					@if ($event->event_status === 'Cancelled')
						<span style="color: gray;"> [Cancelled] - </span><span style="text-decoration: line-through;">{{ $event->event_name }}</span>
					@else
						<span style="color: var(--primary-color);">{{ $event->event_name }}</span>
					@endif
				</h1>
				<div class="event-edit-buttons">
					@can('edit', $event)
						<a href="{{ route('edit.event.show', ['event_id' => $event->event_id]) }}" class="event-button">
							Edit
						</a>
						<button type="button" class="event-button2" onclick="openModal(1)">Cancel Event</button>
					@endcan

					@if(Auth::guard('admin')->check() && $event->event_status != 'Cancelled' && $event->event_date > now())
						<button type="button" class="event-button2" onclick="openModal(1)">Cancel Event</button>
					@endcan

					@if(Auth::check())
						@cannot('edit', $event)
							<button onclick="openModal(2)" class="event-button2">
								Report
							</button>
						@endcannot
					@endif

					@can('edit', $event)
						<!-- Use an anchor for Create Poll -->
						<a href="{{ route('poll.create', ['event_id' => $event->event_id]) }}" class="event-button">
        				Create Poll
    				</a>
					@endcan
					

				</div>
			</div>	
					
			<a href="{{ route('artist', ['artist_id' => $event->artist->artist_id]) }}" class="user-event-owner" style="display: flex; align-items: center; margin-top:1rem;">
				<img
					src="{{ $event->artist->member->getProfileImage() }}"
					alt="Profile Picture" 
					style="width: 5rem; height: 5rem; object-fit: cover; border-radius: 50%; margin-right: 1rem; border: 0.15rem solid white; box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.8);"
				>
				<span class ="user-event-owner">by {{ $event->artist->member->display_name }}</span>
			</a>	
			
			<div class="event-page-details">
				<h3>📅 {{ date('d/m/Y - h:i A', strtotime($event->event_date)) }}</h3>
				<div class="small-line"></div>
				<h4>📍 {{ $event->location }}</h4>
				<div class="small-line"></div>

				<div class="title-edit">
					<h5> 👥 {{ $event->ticket_count }} / {{ $event->capacity }} </h5>
					
					@can('seeParticipants', $event)
						@can('edit', $event)
						<a href="{{ route('participants', ['event_id' => $event->event_id]) }}" class="event-button">
							Manage Participants
						</a>
						@endcan

						@cannot('edit', $event)
						<a href="{{ route('participants', ['event_id' => $event->event_id]) }}" class="event-button">
							View Participants
						</a>
						@endcannot
					@endcan	
				</div>

				<div class="small-line"></div>
			</div>
			<div class="event-page-tags">
            @foreach ($event->tags as $tag)
			<a>
                <span class="tag-button"
                style="
                        background: #{{ $tag->color }};
                        color: #fff;
                        border-radius: 12px;
                        padding: 8px 16px;
                        display: inline-block;
                        font-weight: bold;
                        font-size: 14px;
                        text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.2);
                        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                        ">
                {{ $tag->tag_name }}</span></a>
            @endforeach
        </div>
		
			@php
				$eventCancelled = $event->event_status === 'Cancelled';
    			$eventExpired = $event->event_date <= now();
    			$userTicketCount = $event->tickets->where('member_id', auth()->id())->count();
				$eventType = $event->type_of_event;
			@endphp

		@cannot('edit', $event)
			<div class="ticket-buttons">
				@if ($eventCancelled) <button type="submit" class="disabled-btn" disabled>Event Canceled</button>  
				@elseif ($eventExpired) <button type="submit" class="disabled-btn" disabled>Event Expired</button>  
				@elseif($userTicketCount < 10 && $userTicketCount >= 1)
					<div class="button-container">
						<button class="purchased-btn">
							Tickets Purchased: {{ $userTicketCount }}
						</button>
							<button class="buy-tickets-btn2" onclick="openPurchaseModal()">
								Get More Tickets - {{ $event->price }}€
							</button>
					</div>  

				@elseif ($userTicketCount == 10)
					<button type="submit" class="disabled-btn" disabled>Ticket Limit Reached</button>

				@elseif ($eventType == 'Private' && $event->requests->contains('member_id', auth()->id()))
					<button class="disabled-btn" disabled>Waiting for join request approval...</button>

				@elseif ($eventType == 'Private' && !$event->invitations->contains('member_id', auth()->id()))
					<div class="button-container">
						<button type="submit" class="disabled-btn">Private Event</button>
						<form action="{{ route('request-access') }}" method="POST">   
							@csrf
							<input type="hidden" name="event_id" value="{{ $event->event_id }}">
							<button type="submit" class="request-btn">
								Request Access
							</button>    
						</form>	
					</div>  
				@else            
					<button type="button" class="buy-tickets-btn" onclick="openPurchaseModal()">Get Tickets - {{ $event->price }}€</button>
				@endif  

				
			</div>
				<!-- Purchase Modal -->
				<div class = "marg">
				<div id="purchaseModal" class="new-modal" style="display: none;">
					<div class="new-modal-content">
						<span class="close-btn" onclick="closePurchaseModal()">×</span>
						<h2>Confirm Purchase</h2>
						<p>Fill in the details to complete your ticket purchase:</p>
						<form action="{{ route('buy-ticket') }}" method="POST">
							@csrf
							<input type="hidden" name="event_id" value="{{ $event->event_id }}">
				
							<!-- Ticket Count Input -->
							<div class="form-group">
								<label for="ticket-count">Number of Tickets</label>
								<input type="number" name="ticket_count" id="ticket-count" class="form-input" min="1" max="10" value="1" required>
							</div>
				
							<div class="form-group">
								<label for="card-number">Card Number</label>
								<input type="text" name="card_number" id="card-number" class="form-input" maxlength="16" required placeholder="1234 5678 9012 3456" pattern="\d{16}">
							</div>
							<div class="form-group">
								<label for="expiry-date">Expiration Date</label>
								<input type="text" name="expiry_date" id="expiry-date" class="form-input" required placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/\d{2}">
							</div>
							<div class="form-group">
								<label for="cvv">CVV</label>
								<input type="text" name="cvv" id="cvv" class="form-input" maxlength="3" required placeholder="123" pattern="\d{3}">
							</div>


							<div class="form-group2">
								<p>Total Price: <strong id="total-price">{{ $event->price }}€</strong></p>
							</div>
				
							<div class="button-group">
								<button type="button" class="cancel-button-buy" onclick="closePurchaseModal()">Cancel</button>
								<button type="submit" class="confirm-button-buy" id="confirm-button" disabled>Confirm Purchase</button>
							</div>

							<p class="refund-section">
								[Refund Per Ticket: 
								<strong id="refund-per-ticket">{{ number_format($event->price * ($event->refund / 100), 2) }}€</strong>]
							</p>						
						</form>
					</div>
				</div>
				<script>
					document.addEventListener("DOMContentLoaded", function () {
						const ticketCountInput = document.getElementById('ticket-count');
						const totalPriceElement = document.getElementById('total-price');
						const confirmButton = document.getElementById('confirm-button');
						const ticketPrice = parseFloat({{ $event->price }}); 
						const maxTickets = 10; 
						const existingTickets = {{ $userTicketCount }}; 
						const validTicketCount = maxTickets - existingTickets; 

						function validateTicketCount() {
							const ticketCount = parseInt(ticketCountInput.value) || 0;
							
							if (ticketCount <= 0) {
								confirmButton.disabled = true;
								totalPriceElement.textContent = "Please select at least one ticket.";
								totalPriceElement.style.color = "red"; // Red color for error message
								return;
							}
							else if (ticketCount >= 1 && ticketCount <= validTicketCount) {
								confirmButton.disabled = false; 
								const totalPrice = (ticketCount * ticketPrice).toFixed(2);
								totalPriceElement.textContent = `${totalPrice}€`; 
								totalPriceElement.style.color = "#28a745"; 
							} else {
								confirmButton.disabled = true; 
								totalPriceElement.textContent = `Max tickets available: ${validTicketCount}`; 
								totalPriceElement.style.color = "red"; 
							}
						}
						ticketCountInput.addEventListener('input', validateTicketCount);
						validateTicketCount();
					});
				</script>
			</div>
		@endcannot
		</div>

	</div>
	

	<div class="event-page-description">
		<h1>Description:</h1>
			<div class="event-page-text">
				{{ $event->description }}
			</div>
		<div class="purple-line">
	
	</div>

	<div class="description-comments">
		
		<div class="event-page-description">
			<h1>Polls:</h1>
			@if($polls && $polls->count() > 0)
    			@foreach ($polls as $poll)
        			@if ($poll->end_date > \Carbon\Carbon::now())
            			@include('partials.poll', ['poll' => $poll])
        			@elseif ($poll->end_date < \Carbon\Carbon::now())
            			@can('edit', $event) 
                			@include('partials.poll-data', ['poll' => $poll])	
            			@endcan
        			@endif
    			@endforeach
			@else
    			@include('partials.empty')
			@endif
		</div>

		<div class="purple-line"></div>
		
		<div class="event-page-comments">
			
			@auth
				<h1>Comments:</h1>

				<div class="add-your-own-comment comment-form">
					<img src="{{ Auth::user()->getProfileImage() }}" alt="Profile Picture" class="profile-pic">
					<input type="text" id="new-comment" placeholder="Add a comment..." class="comment-input">
					<span class="error-message" id="error-new-comment"></span>

					<label for="file-upload">Upload a file:</label>
					<input type="file" name="file" id="file-upload" class="file-input">
                    <span class="error-message" id="error-file-upload"></span>

					<button class="post-button" data-event-id="{{ $event->event_id }}" onclick="postComment(this)">Post</button>
				</div>
			@else
				@if(!Auth::guard('admin')->check())
					<p><a href="{{ route('login') }}" style="color: #9b4dff;">Login</a> to add a comment.</p>    
				@endif
			@endauth
			<div id="comment-list">
				@include('partials.comment-list', ['comments' => $comments])
			</div>
		</div>

		@include('partials.go-back')
	</div>	
				</div>
@endsection	