import {Card, CardContent, CardHeader, CardTitle} from "@/Pages/Leauge/components/ui/card"
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/Pages/Leauge/components/ui/table"
import {useTeamContext} from "./context/TeamContext"
import {Badge} from "@/Pages/Leauge/components/ui/badge"

export default function LeagueTable() {
    const {teams, currentWeek, isSimulationComplete} = useTeamContext();

    return (
        <Card>
            <CardHeader className="bg-green-700 text-white dark:bg-gray-700">
                <div className="flex justify-between items-center">
                    <CardTitle>League Table</CardTitle>
                    <Badge variant="outline" className="bg-white text-green-800 dark:bg-gray-800 dark:text-white">
                        Week {currentWeek} of 6
                    </Badge>
                </div>
            </CardHeader>
            <CardContent className="pt-4">
                <Table>
                    <TableHeader>
                        <TableRow className="bg-green-100 dark:bg-gray-800">
                            <TableHead className="w-12">Pos</TableHead>
                            <TableHead>Team</TableHead>
                            <TableHead className="text-center">P</TableHead>
                            <TableHead className="text-center">W</TableHead>
                            <TableHead className="text-center">D</TableHead>
                            <TableHead className="text-center">L</TableHead>
                            <TableHead className="text-center">GF</TableHead>
                            <TableHead className="text-center">GA</TableHead>
                            <TableHead className="text-center">GD</TableHead>
                            <TableHead className="text-center">Pts</TableHead>
                            {currentWeek >= 4 && !isSimulationComplete &&
                                <TableHead className="text-center">Win %</TableHead>}
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {teams?.sort((a, b) => {
                            if (b.points !== a.points) return b.points - a.points
                            if (b.goal_difference !== a.goal_difference) return b.goal_difference - a.goal_difference
                            return b.goals_for - a.goals_for
                        }).map((team, index) => (
                            <TableRow key={team.id} className={index === 0 ? "bg-green-50 dark:bg-gray-700" : ""}>
                                <TableCell className="font-medium">{index + 1}</TableCell>
                                <TableCell className="font-medium">
                                    <div className="flex items-center gap-2">
                                        <div
                                            className="w-6 h-6 rounded-full bg-green-700 flex items-center justify-center text-white dark:bg-gray-600">
                                            {team.name.charAt(0)}
                                        </div>
                                        {team.name}
                                    </div>
                                </TableCell>
                                <TableCell className="text-center">{team.played}</TableCell>
                                <TableCell className="text-center">{team.won}</TableCell>
                                <TableCell className="text-center">{team.drawn}</TableCell>
                                <TableCell className="text-center">{team.lost}</TableCell>
                                <TableCell className="text-center">{team.goals_for}</TableCell>
                                <TableCell className="text-center">{team.goals_against}</TableCell>
                                <TableCell className="text-center">{team.goal_difference}</TableCell>
                                <TableCell className="text-center font-bold">{team.points}</TableCell>
                                {currentWeek >= 4 && !isSimulationComplete && (
                                    <TableCell className="text-center">
                                        <Badge
                                            variant={index === 0 ? "default" : "secondary"}>{team.win_probability}%</Badge>
                                    </TableCell>
                                )}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    )
}

