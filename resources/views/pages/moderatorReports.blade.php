<div class="input-group mb-2">
    <div class="input-group-prepend">
        <span class="input-group-text" id="search-addon"><i class="fas fa-search"></i></span>
    </div>
    <input class="form-control" id="search-report" type="text" placeholder="Search..." />
</div>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th scope="col">
                    <a href="#">Review <i class="fas fa-sort"></i></a>
                </th>
                <th scope="col">
                    <a href="#">OP <i class="fas fa-sort"></i></a>
                </th>
                <th scope="col">
                    <a href="#">Reason <i class="fas fa-sort"></i></a>
                </th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody id="reportsTable">
            @each('partials.report', $reports, 'report')
        </tbody>
    </table>
</div>
<nav aria-label="table navigation">
    {{ $reports->links("pagination::bootstrap-4") }}
</nav>