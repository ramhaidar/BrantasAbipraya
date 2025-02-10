<style>
    #table-data {
        font-size: 0.9em;
        white-space: nowrap;
    }

    #table-data td,
    #table-data th {
        vertical-align: middle;
        text-align: center;
    }

    .filter-popup {
        position: fixed;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 7px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 3000;
        max-height: 300px;
        min-width: 200px;
    }

    .filter-popup.right-aligned {
        right: 10px;
    }

    .table-responsive {
        overflow-x: visible !important;
    }

    .checkbox-list {
        padding: 5px;
        padding-right: 15px;
        max-height: 200px;
        overflow-y: auto;
    }
</style>
