import { Order, OrderResponse } from "@/types/index";
import { apiRequest } from "@/services/apiClient";

export async function updateOrderService (orderId: string): Promise<OrderResponse> {
    const response = await apiRequest<OrderResponse>({
        url: '/api/order/edit', 
        method: 'PUT',
        body: JSON.stringify({ orderId : orderId })
    });

    return response;
}

export async function deleteOrderService (orderId: string): Promise<OrderResponse> {

    const response = await apiRequest<OrderResponse>({
        url: '/api/order/delete', 
        method: 'DELETE', 
        body: JSON.stringify({ orderId : orderId })
    });
    
    return response;
}

export async function fetchOrdersService (): Promise<Order[]> {
    const response = await apiRequest<Order[]>({url: '/api/order/history', method: 'GET'});
    return response;
}