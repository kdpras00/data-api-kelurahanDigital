<?php
namespace App\Repositories;

use App\Interfaces\EventParticipantRepositoryInterface;
use App\Models\Event;
use App\Models\EventParticipant;
use Exception;
use Illuminate\Support\Facades\DB;

class EventParticipantRepository implements EventParticipantRepositoryInterface
{

    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute) {
        $query = EventParticipant::where(function ($query) use ($search) {
            // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
            if ($search) {
                // melakukan search
                $query->search($search);
            }
        });

        $query->orderBy('created_at', 'desc');

        $user = auth()->user();
        if ($user->hasRole('head-of-family') && $user->headOfFamily) {
            $query->where('head_of_family_id', $user->headOfFamily->id);
        }

        if ($limit) {
            // untuk membatasi data yang diambil berdasarkan limit
            $query->take($limit);
        }

        if ($execute) {
            // untuk menjalankan query
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage) {
        $query = $this->getAll($search, $rowPerPage, false);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = EventParticipant::where('id', $id);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $event = Event::where('id', $data['event_id'])->first();

            $eventParticipant                    = new EventParticipant();
            $eventParticipant->event_id          = $data['event_id'];
            $eventParticipant->head_of_family_id = $data['head_of_family_id'];
            $eventParticipant->quantity          = $data['quantity'];
            $eventParticipant->total_price       = $event->price * $data['quantity'];
            $eventParticipant->payment_status    = "pending";
            $eventParticipant->save();

            DB::commit();

            return $eventParticipant;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $event = Event::where('id', $data['event_id'])->first();

            $eventParticipant                    = EventParticipant::find($id);
            $eventParticipant->event_id          = $data['event_id'];
            $eventParticipant->head_of_family_id = $data['head_of_family_id'];

            if (isset($data['quantity'])) {
                $eventParticipant->quantity = $data['quantity'];
            } else {
                $data['quantity'] = $eventParticipant->quantity;
            }

            $eventParticipant->total_price    = $event->price * $data['quantity'];
            $eventParticipant->payment_status = $data['payment_status'];
            $eventParticipant->save();

            DB::commit();

            return $eventParticipant;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $eventParticipant = EventParticipant::find($id);
            $eventParticipant->delete();

            DB::commit();

            return $eventParticipant;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
