<style>
    .checkbox-list {
        padding: 5px;
        padding-right: 15px;
        max-height: 200px;
        overflow-y: auto;
    }

    .currency-value {
        text-align: right !important;
        padding-right: 10px !important;
    }

    .filter-popup {
        position: fixed;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 7px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 3000;
        max-height: 100%;
        min-width: 200px;
    }

    .filter-popup.right-aligned {
        right: 10px;
    }

    .img-thumbnail {
        width: 120px;
        height: 120px;
        object-fit: cover;
        cursor: pointer;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .table-responsive {
        overflow-x: visible !important;
    }

    #table-data {
        font-size: 0.9em;
        white-space: nowrap;
    }

    #table-data td,
    #table-data th {
        vertical-align: middle;
        text-align: center;
    }
</style>
