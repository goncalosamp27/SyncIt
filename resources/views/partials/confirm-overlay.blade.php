<!-- Report -->
@if ($id == 2)
<div class="new-modal-content">
    <h2>Report Submission</h2>
    <form action="{{ $route }}" method="POST">
        @csrf

        <label for="message">Reason</label>

        <!-- Wrap input and error in a single div to maintain layout -->
        <div class="input-group">
            <input id="reason" type="text" name="message" placeholder="Write why you are reporting this event" value="{{ old('message') }}">
            @if ($errors->has('message'))
                <div class="error">{{ $errors->first('message') }}</div>
            @endif
        </div>

        <div class="event-modal-buttons">
            <button type="submit" class="confirm-button-cancel">Submit</button>
            <button type="button" class="cancel-button-cancel" onclick="closeModal({{ $id }})">Discard</button>
        </div>
    </form>
</div>

@else
    <div class="new-modal-content">
        <h2>Confirm action</h2>
        <p>{{ $message }}</p>
        <div class="event-modal-buttons">
            <form action="{{ $route }}" method="POST">
                @csrf
                <button type="submit" class="confirm-button-cancel">Confirm</button>
            </form>
            <button type="button" class="cancel-button-cancel" onclick="closeModal({{ $id }})">Go Back</button>
        </div>
    </div>
@endif
