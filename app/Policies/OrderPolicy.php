<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class OrderPolicy
{
    public function complete(User $user, Order $order): bool
    {
        return $user->type === 'employee' && $order->status === 'pending';
    }

    public function viewPending(User $user)
    {
        return $user->type === 'employee' || $user->type === 'board';
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->type === 'board' && $order->status === 'pending';
    }

    public function viewReceipt(User $user, Order $order): bool
    {
        // Só o membro que fez a encomenda pode descarregar o recibo
        return $user->id === $order->member_id;
    }


    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Só permite ver se for funcionário/admin e se for uma encomenda pendente
        return in_array($user->type, ['employee', 'board']) && $order->status === 'pending';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
