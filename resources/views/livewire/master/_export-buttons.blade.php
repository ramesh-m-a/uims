<div class="flex items-center gap-2">
    <button wire:click="exportCopy"  class="btn btn-light btn-xlg" title="Copy To Clipboard">
        <i class="fa fa-copy fa-lg text-purple-600 !text-1xl"></i>
    </button>

    <button wire:click="exportExcel" class="btn btn-light btn-lg" title="Export to Excel">
        <i class="fa fa-file-excel fa-lg  text-green-600 !text-1xl"></i>
    </button>

    <button wire:click="exportCsv"   class="btn btn-light btn-lg" title="Export to CSV">
        <i class="fa fa-file-csv fa-lg  text-orange-500 !text-1xl"></i>
    </button>

    <button wire:click="exportPdf"   class="btn btn-light btn-lg" title="Export to PDF">
        <i class="fa fa-file-pdf fa-lg  text-red-500 !text-1xl"></i>
    </button>
</div>
