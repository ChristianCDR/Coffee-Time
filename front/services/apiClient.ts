import axios, { AxiosResponse } from 'axios';
import { RequestProps } from "@/types/index";

const apiClient = axios.create({
    baseURL: 'http://localhost:8001',
    headers: {
        'Content-Type': 'application/json'
    }
});

export async function apiRequest<T>({ url, method, body }: RequestProps): Promise<T> {
    const response: AxiosResponse<T> =  await apiClient({ 
        url, 
        method, 
        data: body 
    });

    return response.data;
}

axios.interceptors.response.use((response: AxiosResponse) => {
    return response;
}, function (error) {
    console.error('Erreur API', error);
    return Promise.reject(error);
});