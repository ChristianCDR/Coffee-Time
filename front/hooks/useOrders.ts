import { useEffect, useState } from "react";
import { Order, UseOrdersReturn } from "@/types/index";
import { updateOrderService, deleteOrderService, fetchOrdersService } from "@/services/historyService";

export function useOrders (): UseOrdersReturn {

    const [data, setData] = useState<Order[]>([]);
    const [orderId, setOrderId] = useState<string | null>(null);
    const [message, setMessage] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const fetchOrders = async (): Promise<void> => {
            try {
                setLoading(true);
                const data = await fetchOrdersService();
                setData(data);
            }
            catch (error) {
                setError("Historique vide !");
            }
            finally { 
                setLoading(false); 
            }
        }

        fetchOrders();
    }, [orderId])

    const deleteOrder = async (orderId: string): Promise<void>  => {
        try {
            setLoading(true);
            const data = await deleteOrderService(orderId);
            setOrderId(data.orderId)
            setMessage('Commande supprimée!');
        }
        catch (error) {
            setError("Erreur lors de la suppression de la commande.");
        }
        finally { 
            setLoading(false); 
        }
    }

    const updateOrder = async (orderId: string): Promise<void>  => {
        try {
            setLoading(true);
            const data = await updateOrderService(orderId);
            setOrderId(data.orderId)
            setMessage('Commande terminée!');
        }
        catch (error) {
            setError("Erreur lors de la mise à jour de la commande.");
        }
        finally { 
            setLoading(false); 
        }
    }

    return { deleteOrder, updateOrder, data, message, error, loading };
}