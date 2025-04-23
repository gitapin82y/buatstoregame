<!-- resources/views/components/data-table.blade.php -->
<div {{ $attributes }}>
    <div class="table-responsive">
        <table class="table table-striped" id="{{ $id ?? 'dataTable' }}">
            <thead>
                <tr>
                    {{ $header }}
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>