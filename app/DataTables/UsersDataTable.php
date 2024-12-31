<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['user', 'last_login_at'])
            ->editColumn('user', function (User $user) {
                return view('pages/apps.user-management.users.columns._user', compact('user'));
            })
            ->editColumn('role', function (User $user) {
                return ucwords($user->roles->first()?->name);
            })
            ->editColumn('last_login_at', function (User $user) {
                return sprintf('<div class="badge badge-light fw-bold">%s</div>',
                $user->last_login_at 
                    ? $user->last_login_at->locale('es')->diffForHumans() 
                    : $user->updated_at->locale('es')->diffForHumans());
            })
            ->editColumn('created_at', function (User $user) {
                return $user->created_at
                    ? $user->created_at->locale('es')->isoFormat('D MMMM YYYY, h:mm a') 
                    : '';
            })
            ->addColumn('action', function (User $user) {
                return view('pages/apps.user-management.users.columns._actions', compact('user'));
            })
            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12'tr>><'d-flex justify-content-between'<'col-sm-12 col-md-5'i><'d-flex justify-content-between'p>>",)
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(2)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/user-management/users/columns/_draw-scripts.js')) . "}")
            ->language([
                'sProcessing'     => 'Procesando...',
                'sLengthMenu'     => 'Mostrar _MENU_ registros',
                'sZeroRecords'    => 'No se encontraron resultados',
                'sEmptyTable'     => 'No hay datos disponibles en la tabla',
                'sInfo'           => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                'sInfoEmpty'      => 'Mostrando 0 a 0 de 0 registros',
                'sInfoFiltered'   => '(filtrado de _MAX_ registros en total)',
                'sSearch'         => 'Buscar:',
                'sUrl'            => '',
                'sInfoThousands'  => ',',
                'sLoadingRecords' => 'Cargando...',
                'oPaginate'       => [
                    'sFirst'    => 'Primera',
                    'sPrevious' => 'Anterior',
                    'sNext'     => 'Siguiente',
                    'sLast'     => 'Última'
                ],
                'oAria' => [
                    'sSortAscending'  => ': Activar para ordenar la columna de manera ascendente',
                    'sSortDescending' => ': Activar para ordenar la columna de manera descendente'
                ]
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('user')->addClass('d-flex align-items-center')->name('name')->title('Usuario'), // Solo cambié el título visible
            Column::make('role')->searchable(false),
            Column::make('last_login_at')->title('Última actualización'),
            Column::make('created_at')->title('Fecha creación')->addClass('text-nowrap'),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->title('Acciones')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
