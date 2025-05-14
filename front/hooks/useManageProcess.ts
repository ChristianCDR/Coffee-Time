import { useState } from "react";
import { ProcessResponse, UseManageProcess} from "@/types/index";
import { apiRequest } from "@/services/apiClient";

export function useManageProcess (): UseManageProcess {
    const [message, setMessage] = useState<string | null>(null);

    const manageProcess = async (action: string) => {
        setMessage("Loading...");

        try {
            const response: ProcessResponse = await apiRequest({
                url: `/api/admin/${action}-process`, 
                method: "POST"
            });

            if (response.status) {
                setMessage(response.status);
                // window.location.reload();
            }
            else {
                setMessage("Une erreur est survenue.");
            }
        }
        catch (error) {
            console.error("Request failed", error);
            setMessage("Un problème est survenu lors de la requête.");
        }
    }

    return { message, manageProcess };
}