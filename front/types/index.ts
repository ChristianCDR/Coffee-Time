export interface CoffeeProgressData {
    progress: number;
    orderId: string;
}

export interface Queue {
    node: string;
    messages: number;
}

export interface DateObject {
    date: string;
}

export interface Order {
    orderId: string;
    name: string;
    size: string;
    intensity: string;
    createdAt: DateObject;
    executedAt: DateObject;
}

export interface OrderResponse {
    orderId: string;
}

export interface RequestProps {
    url: string;
    method: 'GET' | 'POST' | 'PUT' | 'DELETE';
    body?: Object;
}

export interface UseOrdersReturn {
    deleteOrder: (orderId: string) => Promise<void>;
    updateOrder: (orderId: string) => Promise<void>;
    data: Order[];
    message: string | null;
    error: string | null;
    loading: boolean;
}

export type UseCoffeeProgressReturn<T> = {
    data: T | null;
    error: string | null;
}

export interface Props {
    className: string;
}

export interface UseToggleMenuReturn {
    open: boolean;
    toggleMenu: () => void 
}

export interface UseQueueStatusReturn { 
    queue: Queue;
    error: string | null;
}

export interface ProcessResponse {
    status: string;
}

export interface UseManageProcess {
    message: string | null;
    manageProcess: (action: string) => Promise<void>
}
