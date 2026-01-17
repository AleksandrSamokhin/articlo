#!/bin/bash

set -e

if [ -z "$1" ]; then
	echo "Usage: $0 <iterations>"
	exit 1
fi

for ((i = 0; i < $1; i++)); do
	echo "Iteration $i"
	echo "================================================"
	result=$(claude --permission-mode acceptEdits "@TASKS.md" <<'EOF'
1. Read the `TASKS.md` file and find the highest-priority task to work on and work only on that task.
2. Find the highest-priority task to work on and work only on that task.
3. Include tests, where practical.
4. Run `composer test` to confirm the task is complete.
5. Make a git commit of that task.
6. Update `TASKS.md` and summarize what you did in the "Progress" section.
7. If while working on the task, you notice it is completed, output <promise>COMPLETE</promise>.
EOF
)

	echo "$result"

	if [[ "$result" == *"<promise>COMPLETE</promise>"* ]]; then
		echo "Task completed. Exiting."
		exit 0
	fi
done