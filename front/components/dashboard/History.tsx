'use client';

import { useOrders } from "@/hooks/useOrders";
import { formatDate } from "@/utils/formatDate";

export default function History () {

    const { deleteOrder, updateOrder, data, message, error, loading } = useOrders();

    return(
        <div>
            <h1 className="font-bold text-center my-5">Historique des commandes</h1>
            { loading && <p className="text-center my-5 font-bold">Loading...</p> }
            { message && <p className="text-center my-5 font-bold">{message}</p> }
            { error && <p className="text-center my-5 font-bold text-red-600">{error}</p> }
            { data.length > 0 &&
                <table>
                    <tbody>
                        <tr>
                            <th scope="col" className="w-sm text-center">N° commande</th>
                            <th scope="col" className="w-sm text-center">Type</th>
                            <th scope="col" className="w-sm text-center">Intensité</th>
                            <th scope="col" className="w-sm text-center">Taille</th>
                            <th scope="col" className="w-sm text-center">Commandé le</th>
                            <th scope="col" className="w-sm text-center">Traitement</th>
                            <th scope="col" className="w-sm text-center">Actions</th>
                        </tr>
                        { data.map((value, index) => (
                            <tr key={value.orderId} className="h-20">
                                <td className="border w-sm text-center"> {value.orderId} </td>
                                <td className="border w-sm capitalize text-center"> {value.name} </td>
                                <td className="border w-sm capitalize text-center"> {value.intensity} </td>
                                <td className="border w-sm capitalize text-center"> {value.size} </td>
                                <td className="border w-sm text-center"> {formatDate(value.createdAt.date)} </td>
                                <td className="border w-sm text-center"> {value.executedAt ? formatDate(value.executedAt.date): 'En cours'} </td>
                                <td className="border"> 
                                    <div className="flex flex-row justify-around w-30">
                                        <svg xmlns="http://www.w3.org/2000/svg" role="button" aria-label="Boutton de suppression" width="28" height="28" viewBox="0 0 16 16" className="hover:text-red-500" onClick={() => deleteOrder(value.orderId)}><path fill="currentColor" d="M2 5v10c0 .55.45 1 1 1h9c.55 0 1-.45 1-1V5zm3 9H4V7h1zm2 0H6V7h1zm2 0H8V7h1zm2 0h-1V7h1zm2.25-12H10V.75A.753.753 0 0 0 9.25 0h-3.5A.753.753 0 0 0 5 .75V2H1.75a.75.75 0 0 0-.75.75V4h13V2.75a.75.75 0 0 0-.75-.75M9 2H6v-.987h3z"/></svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" role="button" aria-label="Bouton de mise à jour" width="28" height="28" viewBox="0 0 16 16" className="hover:text-yellow-200" onClick={() => updateOrder(value.orderId)}><path fill="currentColor" d="M10.733 2.56a1.914 1.914 0 0 1 2.707 2.708L12.707 6l.263.262a1.75 1.75 0 0 1 0 2.475l-1.116 1.116a.5.5 0 0 1-.708-.707l1.117-1.116a.75.75 0 0 0 0-1.061L12 6.708l-5.955 5.954a1.65 1.65 0 0 1-.644.398l-2.743.915a.5.5 0 0 1-.632-.633L2.94 10.6a1.65 1.65 0 0 1 .398-.644z"/></svg>
                                    </div>
                                </td>
                            </tr>
                        ))
                            
                        }
                    </tbody>
                </table>
            }
        </div>
    );
}