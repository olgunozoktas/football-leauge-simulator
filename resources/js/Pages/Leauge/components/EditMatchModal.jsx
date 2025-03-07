import { useState, useEffect } from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/Pages/Leauge/components/ui/dialog";
import { Button } from "@/Pages/Leauge/components/ui/button";
import { Input } from "@/Pages/Leauge/components/ui/input";
import { Label } from "@/Pages/Leauge/components/ui/label";
import { Alert, AlertDescription } from "@/Pages/Leauge/components/ui/alert";
import { CheckCircle } from "lucide-react";

export default function EditMatchModal({ match, onSave, trigger }) {
    const [homeGoals, setHomeGoals] = useState(0);
    const [awayGoals, setAwayGoals] = useState(0);
    const [showSuccess, setShowSuccess] = useState(false);
    const [open, setOpen] = useState(false);

    useEffect(() => {
        if (match) {
            setHomeGoals(match.home_goals || 0);
            setAwayGoals(match.away_goals || 0);
        }
    }, [match]);

    const handleSaveResult = async () => {
        try {
            await onSave(match.id, Number.parseInt(homeGoals), Number.parseInt(awayGoals));
            setShowSuccess(true);

            setTimeout(() => {
                setShowSuccess(false);
                setOpen(false);
            }, 3000);
        } catch (error) {
            console.error("Error saving match result:", error);
        }
    };

    if (!match) return null;

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild onClick={() => {
                setShowSuccess(false);
                setOpen(true);
            }}>
                {trigger}
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Match Result</DialogTitle>
                </DialogHeader>

                {showSuccess && (
                    <Alert className="bg-green-50 border-green-200 text-green-800 mb-4">
                        <CheckCircle className="h-4 w-4 text-green-600" />
                        <AlertDescription>
                            Match result updated successfully!
                        </AlertDescription>
                    </Alert>
                )}

                <div className="grid grid-cols-3 gap-4 py-4 items-center">
                    <div className="text-center">
                        <p className="font-medium">{match.home_team?.name}</p>
                        <div className="flex flex-col items-center mt-1 mb-3">
                            <div className="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                Power: {match.home_team?.power}
                            </div>
                            <div className="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div
                                    className="h-full bg-green-600"
                                    style={{ width: `${(match.home_team?.power / 100) * 100}%` }}
                                ></div>
                            </div>
                        </div>
                        <Label htmlFor="homeGoals" className="sr-only">
                            Home Goals
                        </Label>
                        <Input
                            id="homeGoals"
                            type="number"
                            min="0"
                            value={homeGoals}
                            onChange={(e) => setHomeGoals(e.target.value)}
                            className="w-16 mx-auto mt-2"
                        />
                    </div>
                    <div className="text-center text-2xl font-bold">vs</div>
                    <div className="text-center">
                        <p className="font-medium">{match.away_team?.name}</p>
                        <div className="flex flex-col items-center mt-1 mb-3">
                            <div className="text-xs text-gray-600 dark:text-gray-400 mb-1">
                                Power: {match.away_team?.power}
                            </div>
                            <div className="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div
                                    className="h-full bg-green-600"
                                    style={{ width: `${(match.away_team?.power / 100) * 100}%` }}
                                ></div>
                            </div>
                        </div>
                        <Label htmlFor="awayGoals" className="sr-only">
                            Away Goals
                        </Label>
                        <Input
                            id="awayGoals"
                            type="number"
                            min="0"
                            value={awayGoals}
                            onChange={(e) => setAwayGoals(e.target.value)}
                            className="w-16 mx-auto mt-2"
                        />
                    </div>
                </div>
                <Button onClick={handleSaveResult} disabled={showSuccess}>
                    {showSuccess ? "Saved!" : "Save Result"}
                </Button>
            </DialogContent>
        </Dialog>
    );
}
