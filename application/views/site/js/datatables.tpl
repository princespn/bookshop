<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            info: false,
            "order": [[ 0, "desc" ]]
        });
    } );
</script>