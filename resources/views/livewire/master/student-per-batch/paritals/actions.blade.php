<a href="{{ route('master.student-per-batch.edit', $row->id) }}" class="btn btn-sm btn-warning">Edit</a>

<form action="{{ route('student-per-batch.destroy', $row->id) }}"
      method="POST"
      style="display:inline-block;">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
</form>
