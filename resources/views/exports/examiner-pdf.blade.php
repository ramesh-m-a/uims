<table border="1" width="100%">
    <tr>
        <th>College</th>
        <th>Department</th>
        <th>Name</th>
        <th>Designation</th>
        <th>Rank</th>
    </tr>
    @foreach($data as $row)
        <tr>
            <td>{{ $row->mas_college_name }}</td>
            <td>{{ $row->mas_department_name }}</td>
            <td>{{ $row->fname }}</td>
            <td>{{ $row->mas_designation_name }}</td>
            <td>{{ $row->examiner_details_rank }}</td>
        </tr>
    @endforeach
</table>
