<form method="POST"
      action="{{ route('rguhs.profiles.approve', $user) }}">
    @csrf
    <flux:button variant="primary">
        Final Approve
    </flux:button>
</form>

<form method="POST"
      action="{{ route('rguhs.profiles.reject', $user) }}"
      class="mt-3">
    @csrf

    <flux:textarea
        name="remarks"
        placeholder="Reason for rejection"
        required
    />

    <flux:button variant="danger">
        Reject & Send Back to Teacher
    </flux:button>
</form>
