'use client'

import { useEffect, useState } from "react";

type Date = {
    date:  string
}

type Order = {
    orderID: string,
    name: string,
    size: string,
    intensity: string,
    createdAt: Date,
    executedAt: Date
}

export default function History () {
    const [data, setData] = useState<Order[]>([]);
    const [message, setMessage] = useState("");

    useEffect(() => {
        const fetchOrders = async () => {
            const response = await fetch('http://localhost:8001/api/order/history');
            if(!response.ok) {
                console.log(response)
            }
            const data = await response.json();
            // console.log(data)
            setData(data);
        }

        fetchOrders();
    }, [message])

    const formattedDate = (date: string) : string => {
        return date.split('.')[0];
    }

    const deleteOrder= async (orderId: string)  => {
        const response = await fetch('http://localhost:8001/api/order/delete', {
            method: 'DELETE',
            headers : {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ orderId : orderId })
        });

        if(!response.ok) {
            console.log(response);
        }

        const data =  response;
        if (data.status === 204) setMessage('Commande supprimée!');
    }

    const updateOrder= async (orderId: string)  => {
        const response = await fetch('http://localhost:8001/api/order/edit', {
            method: 'PUT',
            headers : {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ orderId : orderId })
        });

        if(!response.ok) {
            console.log(response);
        }

        setMessage('Commande terminée!');
    }

    return(
        <div>
            <h1 className="font-bold text-center my-5">Historique des commandes</h1>
            {message && <p>{message}</p> }
            <table>
                <tbody>
                    <tr>
                        <th scope="col">N° commande</th>
                        <th scope="col">Type</th>
                        <th scope="col">Intensité</th>
                        <th scope="col">Taille</th>
                        <th scope="col">Commandé le</th>
                        <th scope="col">Traitement</th>
                        <th scope="col">Actions</th>
                    </tr>
                    { data && data.map((value, index) => (
                        <tr key={index} className="h-20">
                            <td className="border w-sm text-center"> {value.orderID} </td>
                            <td className="border w-sm capitalize text-center"> {value.name} </td>
                            <td className="border w-sm capitalize text-center"> {value.intensity} </td>
                            <td className="border w-sm capitalize text-center"> {value.size} </td>
                            <td className="border w-sm text-center"> {formattedDate(value.createdAt.date)} </td>
                            <td className="border w-sm text-center"> {value.executedAt ? formattedDate(value.executedAt.date): 'En cours'} </td>
                            <td className="border"> 
                                <div className="flex flex-row justify-around w-30">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 16 16" className="hover:text-red-500" onClick={() => deleteOrder(value.orderID)}><path fill="currentColor" d="M2 5v10c0 .55.45 1 1 1h9c.55 0 1-.45 1-1V5zm3 9H4V7h1zm2 0H6V7h1zm2 0H8V7h1zm2 0h-1V7h1zm2.25-12H10V.75A.753.753 0 0 0 9.25 0h-3.5A.753.753 0 0 0 5 .75V2H1.75a.75.75 0 0 0-.75.75V4h13V2.75a.75.75 0 0 0-.75-.75M9 2H6v-.987h3z"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 16 16" className="hover:text-yellow-200" onClick={() => updateOrder(value.orderID)}><path fill="currentColor" d="M10.733 2.56a1.914 1.914 0 0 1 2.707 2.708L12.707 6l.263.262a1.75 1.75 0 0 1 0 2.475l-1.116 1.116a.5.5 0 0 1-.708-.707l1.117-1.116a.75.75 0 0 0 0-1.061L12 6.708l-5.955 5.954a1.65 1.65 0 0 1-.644.398l-2.743.915a.5.5 0 0 1-.632-.633L2.94 10.6a1.65 1.65 0 0 1 .398-.644z"/></svg>
                                </div>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            
        </div>
    );
}