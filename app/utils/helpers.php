<?php

namespace App\utils;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class helpers
{
    /**
     * Apply request filters to a query.
     *
     * @param mixed $model
     * @param array $columns
     * @param array $param
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function filter($model, $columns, $param, $request)
    {
        foreach ($columns as $index => $column) {

            if (!$request->filled($column)) {
                continue;
            }

            $operator = $param[$index] ?? '=';

            if (strtolower($operator) === 'like') {
                $model->where($column, 'LIKE', '%' . $request->$column . '%');
            } else {
                $model->where($column, $operator, $request->$column);
            }
        }

        return $model;
    }

    /**
     * Restrict records if the user lacks permission.
     *
     * @param mixed $model
     * @return mixed
     */
    public function Show_Records($model)
    {
        $user = Auth::user();

        if (!$user) {
            return $model;
        }

        $role = $user->roles()->first();

        if (!$role) {
            return $model->where('user_id', Auth::id());
        }

        $showRecord = $role->inRole('record_view');

        if (!$showRecord) {
            return $model->where('user_id', Auth::id());
        }

        return $model;
    }

    /**
     * Get current settings with currency.
     *
     * @return \App\Models\Setting|null
     */
    private function settings()
    {
        return Setting::with('Currency')
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Get currency symbol.
     *
     * @return string
     */
    public function Get_Currency()
    {
        $settings = $this->settings();

        if (!$settings || !$settings->Currency) {
            return '';
        }

        return $settings->Currency->symbol ?? '';
    }

    /**

     *
     * @return string
     */
    public function Get_Currency_Code()
    {
        $settings = $this->settings();

        if (!$settings || !$settings->Currency) {
            return 'USD';
        }

        return strtoupper($settings->Currency->code ?? 'USD');
    }
}
